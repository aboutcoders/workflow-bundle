<?php
namespace Abc\Bundle\WorkflowBundle\Executable;

use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\Executable;
use Abc\Bundle\JobBundle\Model\JobInterface;
use Abc\Bundle\WorkflowBundle\Entity\Task;
use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Model\TaskInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
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
        $this->workflowManager   = $workflowManager;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Job $job)
    {
        if(!$job->getParameters() instanceof WorkflowInterface)
        {
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
        if($job->isTriggeredByCallback())
        {
            $job->getContext()->get('logger')->debug('Callback: {ticket}', array('ticket' => $job->getCallerJob()->getTicket()));
        }
        else
        {
            /** @var Workflow $workflow */
            $workflow = $job->getParameters();

            $job->getContext()->get('logger')->debug('Workflow: {workflow} ', array('workflow' => $workflow->getId()));

            $tasks = $this->taskManager->findWorkflowTasks($workflow->getId());
            if(count($tasks) > 0)
            {
                $job->getContext()->get('logger')->debug('Processing tasks...');
                /** @var Task $task */
                foreach($tasks as $task)
                {
                    $this->addTask($job, $task);
                }
            }
        }

    }

    /**
     * @param Job $job
     * @return void
     */
    protected function executeSequentially(Job $job)
    {
        /** @var Workflow $workflow */
        $workflow   = $job->getParameters();
        $workflowId = $workflow->getId();
        $index      = 0;

        if(!$job->isTriggeredByCallback())
        {
            $job->getContext()->get('logger')->debug('Start executing workflow {workflowId}', array('workflowId' => $workflowId));

            # start execution
            $this->createExecution($job->getTicket(), $workflowId);

            # store parameters in the context
            $job->getContext()->set('parameters', $workflow->getParameters());
        }
        else
        {
            $job->getContext()->get('logger')->debug('Callback by ticket {ticket}', array('ticket' => $job->getCallerJob()->getTicket()));

            $index = $workflow->getIndex() + 1;
        }

        if($task = $this->taskManager->findNextWorkflowTask($workflowId, $index))
        {
            $this->addTask($job, $task);
        }
        else
        {
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
        $jobType = $task->getType()->getJobType();

        $ticket = $job->addChildJob($jobType, $task->getParameters(), $task->getSchedule());

        $job->getContext()->get('logger')->debug('Added child job of type {type} with ticket {ticket}', array('type' => $jobType, 'ticket' => $ticket));
    }

    /**
     * @param string  $ticket
     * @param integer $workflowId
     */
    protected function createExecution($ticket, $workflowId)
    {
        $execution = $this->executionManager->create();

        $workflow = $this->workflowManager->findById($workflowId);

        $execution->setWorkflow($workflow);
        $execution->setTicket($ticket);

        $this->executionManager->update($execution);
    }
}