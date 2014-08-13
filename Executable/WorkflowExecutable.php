<?php

namespace Abc\Bundle\WorkflowBundle\Executable;

use Abc\Bundle\JobBundle\Job\Exception\TerminateException;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\Executable;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\JobBundle\Model\JobInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Monolog\Logger;

class WorkflowExecutable implements Executable
{
    /** @var TaskManagerInterface */
    protected $taskManager;
    /** @var ExecutionManagerInterface */
    protected $executionManager;
    /** @var WorkflowManagerInterface */
    protected $workflowManager;


    /**
     * @param WorkflowManagerInterface  $workflowManager
     * @param TaskManagerInterface      $taskManager
     * @param ExecutionManagerInterface $executionManager
     */
    function __construct(WorkflowManagerInterface $workflowManager, TaskManagerInterface $taskManager, ExecutionManagerInterface $executionManager)
    {
        $this->executionManager = $executionManager;
        $this->taskManager      = $taskManager;
        $this->workflowManager  = $workflowManager;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Job $job)
    {
        if (!$job->getParameters() instanceof WorkflowInterface) {
            throw new \InvalidArgumentException('Parameters must be an instance of Abc\Bundle\WorkflowBundle\Model\WorkflowInterface');
        }

        $this->executeSequentially($job);
    }

    /**
     * @param Job $job
     * @return void
     * @codeCoverageIgnore
     */
    protected function executeSimultaneously(Job $job)
    {
        if ($job->isTriggeredByCallback()) {
            $job->getContext()->get('logger')->debug('Callback: {ticket}', array('ticket' => $job->getCallerJob()->getTicket()));
        } else {
            /** @var WorkflowInterface $workflow */
            $workflow = $job->getParameters();

            $job->getContext()->get('logger')->debug('Workflow: {workflow} ', array('workflow' => $workflow->getId()));

            $tasks = $this->taskManager->findWorkflowTasks($workflow->getId());
            if (count($tasks) > 0) {
                $job->getContext()->get('logger')->debug('Processing tasks...');
                /** @var TaskInterface $task */
                foreach ($tasks as $task) {
                    $this->addTask($job, $task);
                }
            }
        }

    }

    /**
     * @param Job $job
     * @throws \Exception
     * @return void
     */
    protected function executeSequentially(Job $job)
    {
        /** @var WorkflowInterface $workflow */
        $workflow   = $job->getParameters();
        $workflowId = $workflow->getId();
        $index      = 0;

        if (!$job->isTriggeredByCallback()) {
            $job->getContext()->get('logger')->debug('Start executing workflow with id {id}', array('id' => $workflowId));

            # store parameters in the context
            $job->getContext()->set('parameters', $workflow->getParameters());

            # start execution
            $this->createExecution($job->getTicket(), $workflowId);
        } else {
            if ($job->getCallerJob()->getStatus() != Status::PROCESSED()) {
                $job->getContext()->get('logger')->debug('Child job {ticket} terminated with status {status}', array('ticket', $job->getCallerJob()->getTicket(), 'status' => $job->getCallerJob()->getStatus()->getName()));

                throw new TerminateException('Abort execution (a child job terminated unsuccessfully)');
            }

            $job->getContext()->get('logger')->debug('Callback by ticket {ticket}', array('ticket' => $job->getCallerJob()->getTicket()));

            $index = $workflow->getIndex() + 1;
        }

        if ($task = $this->taskManager->findNextWorkflowTask($workflowId, $index)) {
            $this->addTask($job, $task);
        } else {
            $job->getContext()->get('logger')->debug('No tasks to execute');
        }

        $workflow->setIndex($index);
        $job->setParameters($workflow);
        $job->update();
    }

    /**
     * @param Job           $job
     * @param TaskInterface $task
     */
    protected function addTask(Job $job, TaskInterface $task)
    {
        $ticket = $job->addChildJob($task->getType()->getJobType(), $task->getParameters(), $task->getSchedule());

        $job->getContext()->get('logger')->debug(
            'Added child job {ticket} {type} {parameters} {schedule}',
            array('ticket' => $ticket, 'type' => $task->getType()->getJobType(), 'parameters' => $task->getParameters(), 'schedule' => $task->getSchedule())
        );
    }

    /**
     * @param string  $ticket
     * @param integer $workflowId
     * @return void
     */
    protected function createExecution($ticket, $workflowId)
    {
        if ($this->executionManager->findOneBy(array('ticket' => $ticket)) == null) {
            $workflow = $this->workflowManager->findById($workflowId);

            $this->executionManager->execute($ticket, $workflow);
        }
    }
}