<?php
namespace Abc\Bundle\WorkflowBundle\Executable;

use Abc\Bundle\JobBundle\Api\Executable;
use Abc\Bundle\JobBundle\Api\Job;
use Abc\Bundle\JobBundle\Model\JobInterface;
use Abc\Bundle\WorkflowBundle\Entity\Task;
use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Model\TaskInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeInterface;
use Monolog\Logger;

class WorkflowExecutable implements Executable
{
    /** @var TaskManagerInterface */
    protected $taskManager;

    /**
     * @param TaskManagerInterface $taskManager
     */
    function __construct(TaskManagerInterface $taskManager)
    {
        $this->taskManager = $taskManager;
    }

    /**
     * @param Job $job
     * @return void
     */
    public function execute(Job $job)
    {
        $this->executeSequentially($job);
    }

    /**
     * @param Job $job
     * @return void
     */
    protected function executeSimultaneously(Job $job)
    {
        if ($job->isCallback()) {
            $job->getContext()->get('logger')->debug('Callback: {ticket}', array('ticket' => $job->getCallback()->getTicket()));
        } else {
            /** @var Workflow $workflow */
            $workflow = $job->getParameters();

            $job->getContext()->get('logger')->debug('Workflow: {workflow} ', array('workflow' => $workflow->getId()));
            $tasks = $this->taskManager->findWorkflowTasks($workflow->getId());
            if (count($tasks) > 0) {
                $job->getContext()->get('logger')->debug('Processing tasks...');
                /** @var Task $task */
                foreach ($tasks as $task) {
                    $this->addTask($job, $task);
                }
            }
        }

    }

    /**
     * @param Job $job
     * @return void
     */
    protected function executeSequentially($job)
    {
        /** @var Workflow $workflow */
        $workflow   = $job->getParameters();
        $workflowId = $workflow->getId();

        $job->getContext()->set('parameters', $workflow->getParameters());
        $job->getContext()->get('logger')->debug('Workflow: {workflow} ', array('workflow' => $workflowId));

        if ($job->isCallback()) {
            $job->getContext()->get('logger')->debug('Callback: {ticket}', array('ticket' => $job->getCallback()->getTicket()));
            $index = $workflow->getIndex() + 1;
            $task  = $this->taskManager->findNextWorkflowTask($workflowId, $index);
            $job->getContext()->get('logger')->debug('Task: {task}', array('task' => $task));
        } else {
            $job->getContext()->get('logger')->debug('Initial execution');
            $task  = $this->taskManager->findNextWorkflowTask($workflowId);
            $index = 0;
        }
        $workflow->setIndex($index);
        $job->getContext()->get('logger')->debug('Index: {index}', array('index' => $index));

        $job->updateParameters($workflow);
        if ($task) {
            $this->addTask($job, $task);
        } else {
            $job->getContext()->get('logger')->debug('No tasks to execute');
        }
    }

    /**
     * @param Job           $job
     * @param TaskInterface $task
     */
    protected function addTask(Job $job, TaskInterface $task)
    {
        if (!$task->isDisabled()) {
            $jobType = strtolower($task->getType()->getName());
            $job->getContext()->get('logger')->debug('Job type: {type}', array('type' => $jobType));
            $ticket = $job->addChildJob($jobType, $task->getParameters());
            $job->getContext()->get('logger')->debug('Ticket: {ticket}', array('ticket' => $ticket));
        } else {
            $job->getContext()->get('logger')->debug('Task {ticket} disabled', array('ticket' => $job->getTicket()));
        }
    }
}