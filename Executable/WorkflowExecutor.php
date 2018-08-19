<?php

namespace Abc\Bundle\WorkflowBundle\Executable;

use Abc\Bundle\JobBundle\Job\JobInterface;
use Abc\ProcessControl\ControllerInterface;
use Abc\Bundle\JobBundle\Job\ManagerInterface;
use Abc\Bundle\JobBundle\Annotation\ParamType;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Configuration;
use Abc\Bundle\WorkflowBundle\Workflow\Response;
use Abc\ProcessControl\ControllerAwareInterface;
use Abc\Bundle\JobBundle\Job\JobAwareInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class WorkflowExecutor implements LoggerAwareInterface, ControllerAwareInterface, JobAwareInterface
{
    /** @var LoggerInterface */
    private $logger;
    /** @var ControllerInterface */
    private $controller;
    /** @var JobInterface */
    private $job;

    /** @var TaskManagerInterface */
    protected $taskManager;
    /** @var WorkflowManagerInterface */
    protected $workflowManager;


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
     * @ParamType("manager", type="@abc.manager")
     * @param Configuration    $configuration
     * @param ManagerInterface $manager
     * @throws \Exception
     */
    public function execute(Configuration $configuration, ManagerInterface $manager)
    {
        return $this->executeSequentially($configuration, $manager);
    }

    /**
     * @param Configuration    $configuration
     * @param ManagerInterface $manager
     * @return void
     * @codeCoverageIgnore
     */
    protected function executeSimultaneously(Configuration $configuration, ManagerInterface $manager)
    {
//        if ($job->isTriggeredByCallback()) {
//            $this->logger->debug('Callback: {ticket}', ['ticket' => $job->getCallerJob()->getTicket()]);
//        } else {
        $this->logger->debug('Workflow: {workflow} ', ['workflow' => $configuration->getId()]);

        $tasks = $this->taskManager->findWorkflowTasks($configuration->getId());

        if (count($tasks) > 0) {
            $this->logger->debug('Processing tasks...');
            /** @var TaskInterface $task */
            foreach ($tasks as $task) {
                $this->addTask($manager, $task);
            }
        }
//        }
    }

    /**
     * @param Configuration    $configuration
     * @param ManagerInterface $manager
     * @return void
     */
    protected function executeSequentially(Configuration $configuration, ManagerInterface $manager)
    {
        $workflowId = $configuration->getId();

        $this->logger->debug('Start executing workflow with id {id} {ticket}',
            ['id' => $configuration->getId(), 'ticket' => $this->job->getTicket()]);

//        if (!$job->isTriggeredByCallback()) {
//            $this->logger->debug('Start executing workflow with id {id}', array('id' => $workflowId));
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
//                $this->logger->debug(
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
//            $this->logger->debug('Callback by ticket {ticket}', array('ticket' => $job->getCallerJob()->getTicket()));
//
        $configuration->setIndex($configuration->getIndex() + 1);
//        }

//        $this->controller->doPause();

        if ($task = $this->taskManager->findNextWorkflowTask($workflowId, $configuration->getIndex())) {
            $this->addTask($manager, $task);
        } else {
            $this->logger->debug('No tasks to execute');
        }

//        return $this->controller->doStop();
//        $job->update();
        $manager->update($this->job);
    }

    /**
     * @param ManagerInterface $manager
     * @param TaskInterface    $task
     */
    protected function addTask(ManagerInterface $manager, TaskInterface $task)
    {
//        $response = $job->getResponseBody();
//        if ($response instanceof Response) {
//            $response->addAction(strtoupper($task->getType()->getName()));
//        }

        $ticket = $manager->addJob($task->getType()->getJobType(), $task->getParameters(), $task->getSchedule());

        $this->logger->debug(
            'Added child job {ticket} {type} {parameters} {schedule}',
            ['ticket' => $ticket, 'type' => $task->getType()->getJobType(), 'parameters' => $task->getParameters(), 'schedule' => $task->getSchedule()]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setController(ControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function setJob(JobInterface $job)
    {
        $this->job = $job;
    }
}