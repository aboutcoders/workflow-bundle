<?php

namespace Abc\Bundle\WorkflowBundle\Workflow;

use Abc\Bundle\JobBundle\Job as Job;
use Abc\Bundle\JobBundle\Job\Exception\TicketNotFoundException;
use Abc\Bundle\JobBundle\Job\Report\ReportInterface;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\SchedulerBundle\Model\ScheduleInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Exception\WorkflowNotFoundException;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class Manager implements ManagerInterface
{

    /** @var ExecutionManagerInterface */
    protected $executionManager;
    /** @var Job\ManagerInterface */
    protected $jobManager;
    /** @var TaskManagerInterface */
    protected $taskManager;
    /** @var WorkflowManagerInterface */
    protected $workflowManager;

    /**
     * @param Job\ManagerInterface     $jobManager
     * @param WorkflowManagerInterface $workflowManager
     * @param TaskManagerInterface     $taskManager
     */
    function __construct(Job\ManagerInterface $jobManager, WorkflowManagerInterface $workflowManager, TaskManagerInterface $taskManager)
    {
        $this->jobManager      = $jobManager;
        $this->workflowManager = $workflowManager;
        $this->taskManager     = $taskManager;
    }

    /**
     * {@inheritDoc}
     */
    public function cancel($ticket)
    {
        $this->jobManager->cancelJob($ticket);
    }

    public function create($name, $category = null, $createDirectory = true, $removeDirectory = true)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function execute($id, \Serializable $parameters = null, ScheduleInterface $schedule = null, $response = null)
    {
        $workflow = $this->workflowManager->findById($id);

        if($workflow == null)
        {
            throw new WorkflowNotFoundException($id);
        }

        if($response == null)
        {
            $response = new Response();
        }

        $configuration = new Configuration($id, $parameters, $workflow->getCreateDirectory(), $workflow->getRemoveDirectory());

        $ticket = $this->jobManager->addJob('workflow', $configuration, $schedule, $response);

        $execution = $this->executionManager->create($ticket, $workflow);
        $this->executionManager->update($execution);

        return $execution;
    }

    /**
     * {@inheritDoc}
     */
    public function getProgress($ticket)
    {
        $report = $this->getReport($ticket);

        if($report->getStatus() == Status::PROCESSED() || $report->getStatus() == Status::CANCELLED() || $report->getStatus() == Status::ERROR())
        {
            return 100;
        }

        /** @var Configuration $configuration */
        $configuration = $report->getParameters();
        $index         = $configuration->getIndex();
        $tasks         = $this->taskManager->findWorkflowTasks($configuration->getId());
        $total         = count($tasks);

        $progress = 100 - (($total - $index) / $total * 100);

        return (int)round($progress);
    }

    /**
     * {@inheritDoc}
     */
    public function getReport($ticket)
    {
        return $this->jobManager->getReport($ticket);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus($ticket)
    {
        return $this->jobManager->getStatus($ticket);
    }

    /**
     * @param ExecutionManagerInterface $manager
     */
    public function setExecutionManager(ExecutionManagerInterface $manager)
    {
        $this->executionManager = $manager;
    }
}