<?php

namespace Abc\Bundle\WorkflowBundle\Executable;

use Abc\Bundle\JobBundle\Job\Exception\TerminateException;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\Response\ErrorResponse;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\JobBundle\Model\JobInterface;
use Abc\Bundle\JobBundle\Annotation\ParamType;
use Abc\Bundle\WorkflowBundle\Model\TaskInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Configuration;
use Abc\Bundle\WorkflowBundle\Workflow\Response;
use League\Flysystem\MountManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class WorkflowExecutor implements LoggerAwareInterface
{
    /** @var TaskManagerInterface */
    protected $taskManager;
    /** @var WorkflowManagerInterface */
    protected $workflowManager;
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param WorkflowManagerInterface $workflowManager
     * @param TaskManagerInterface     $taskManager
     */
    function __construct(WorkflowManagerInterface $workflowManager, TaskManagerInterface $taskManager)
    {
        $this->taskManager     = $taskManager;
        $this->workflowManager = $workflowManager;
        $this->logger          = new NullLogger();
    }

    /**
     * @ParamType("configuration", type="Abc\Bundle\WorkflowBundle\Workflow\Configuration")
     */
    public function execute(Configuration $configuration)
    {
        $this->executeSequentially($configuration);
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
            /** @var Configuration $configuration */
            $configuration = $job->getParameters();

            $job->getContext()->get('logger')->debug('Workflow: {workflow} ', array('workflow' => $configuration->getId()));

            $tasks = $this->taskManager->findWorkflowTasks($configuration->getId());

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
    protected function executeSequentially(Configuration $configuration)
    {
        $workflowId = $configuration->getId();

//        if (!$job->isTriggeredByCallback()) {
//            $job->getContext()->get('logger')->debug('Start executing workflow with id {id}', array('id' => $workflowId));
//
//            # store parameters in the context
//            $job->getContext()->set('parameters', $configuration->getParameters());
//        } else {
//            $response = $job->getResponseBody();
//            if ($response instanceof Response) {
//                $response->setActions(array());
//            }
//
//            if ($job->getCallerJob()->getStatus() != Status::PROCESSED()) {
//                $job->getContext()->get('logger')->debug(
//                    'Child job {ticket} terminated with status {status}',
//                    array('ticket', $job->getCallerJob()->getTicket(), 'status' => $job->getCallerJob()->getStatus()->getName())
//                );
//
//                if ($response instanceof ErrorResponse) {
//                    throw new TerminateException($response->getMessage(), $response->getCode());
//                } else {
//                    throw new TerminateException('Abort execution (a child job terminated unsuccessfully)');
//                }
//            }
//
//            $job->getContext()->get('logger')->debug('Callback by ticket {ticket}', array('ticket' => $job->getCallerJob()->getTicket()));
//
//            $configuration->setIndex($configuration->getIndex() + 1);
//        }
//
//        if ($task = $this->taskManager->findNextWorkflowTask($workflowId, $configuration->getIndex())) {
//            $this->addTask($job, $task);
//        } else {
//            $job->getContext()->get('logger')->debug('No tasks to execute');
//        }
//
//        $job->update();
    }

    /**
     * @param Job           $job
     * @param TaskInterface $task
     */
    protected function addTask(Job $job, TaskInterface $task)
    {
        $response = $job->getResponseBody();
        if ($response instanceof Response) {
            $response->addAction(strtoupper($task->getType()->getName()));
        }

        $ticket = $job->addChildJob($task->getType()->getJobType(), $task->getParameters(), $task->getSchedule());

        $job->getContext()->get('logger')->debug(
            'Added child job {ticket} {type} {parameters} {schedule}',
            array('ticket' => $ticket, 'type' => $task->getType()->getJobType(), 'parameters' => $task->getParameters(), 'schedule' => $task->getSchedule())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}