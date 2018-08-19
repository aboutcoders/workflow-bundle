<?php

namespace Abc\Bundle\WorkflowBundle\Workflow;

use Abc\Bundle\JobBundle\Job as Job;
use Abc\Bundle\JobBundle\Job\Exception\TicketNotFoundException;
use Abc\Bundle\JobBundle\Job\Report\ReportInterface;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\SchedulerBundle\Model\ScheduleInterface;
use Abc\Bundle\WorkflowBundle\Model\CategoryManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowReport;
use Abc\Bundle\WorkflowBundle\Workflow\Exception\WorkflowNotFoundException;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class Manager implements ManagerInterface
{
    /** @var  CategoryManagerInterface */
    protected $categoryManager;
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
     * @param CategoryManagerInterface $categoryManager
     */
    function __construct(
        Job\ManagerInterface $jobManager,
        WorkflowManagerInterface $workflowManager,
        TaskManagerInterface $taskManager,
        CategoryManagerInterface $categoryManager
    )
    {
        $this->jobManager      = $jobManager;
        $this->workflowManager = $workflowManager;
        $this->taskManager     = $taskManager;
        $this->categoryManager = $categoryManager;
    }

    /**
     * {@inheritDoc}
     */
    public function create($name, $categoryName = null, $createDirectory = true, $removeDirectory = true)
    {
        if ($categoryName != null && !$this->categoryManager->exists($categoryName)) {
            $category = $this->categoryManager->create();
            $category->setName($categoryName);
            $this->categoryManager->update($category);
        }

        $workflow = $this->workflowManager->create();
        $workflow->setName($name);
        $workflow->setCreateDirectory($createDirectory);
        $workflow->setRemoveDirectory($removeDirectory);
        $workflow->setDisabled(false);
        $this->workflowManager->update($workflow);

        return $workflow->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function cancel($ticket)
    {
        $this->jobManager->cancel($ticket);
    }

    /**
     * {@inheritDoc}
     */
    public function execute($id, \Serializable $parameters = null, ScheduleInterface $schedule = null, $response = null)
    {
        $workflow = $this->workflowManager->findById($id);

        if ($workflow == null) {
            throw new WorkflowNotFoundException($id);
        }

        $configuration = new Configuration($id, $parameters, $workflow->getCreateDirectory(), $workflow->getRemoveDirectory());

        $ticket = $this->jobManager->addJob('workflow', [$configuration], $schedule);

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

        if ($report->getJob()->getStatus() == Status::PROCESSED() || $report->getJob()->getStatus() == Status::CANCELLED() || $report->getJob()->getStatus() == Status::ERROR()) {
            return 100;
        }

        /** @var Configuration $configuration */
        $configuration = $report->getJob()->getParameters()[0];
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

        return new WorkflowReport($this->jobManager->get($ticket), $this->jobManager->getLogs($ticket));
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus($ticket)
    {
        return $this->jobManager->get($ticket)->getStatus();
    }

    /**
     * @param ExecutionManagerInterface $manager
     */
    public function setExecutionManager(ExecutionManagerInterface $manager)
    {
        $this->executionManager = $manager;
    }
}