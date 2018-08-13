<?php

namespace Abc\Bundle\WorkflowBundle\Workflow;

use Abc\Bundle\JobBundle\Job\Exception\TicketNotFoundException;
use Abc\Bundle\JobBundle\Job\JobInterface;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\SchedulerBundle\Model\ScheduleInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowReportInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Exception\WorkflowNotFoundException;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
interface ManagerInterface
{
    /**
     * Creates a new workflow
     *
     * @param string      $name
     * @param string|null $categoryName
     * @param bool        $createDirectory
     * @param bool        $removeDirectory
     * @return int The unique id of the created workflow
     */
    public function create($name, $categoryName = null, $createDirectory = true, $removeDirectory = true);

    /**
     * @param string $ticket
     * @return void
     * @throws TicketNotFoundException
     */
    public function cancel($ticket);

    /**
     * @param int               $id The id of the workflow
     * @param \Serializable     $parameters
     * @param ScheduleInterface $schedule
     * @param mixed|null        $response
     * @return ExecutionInterface
     * @throws WorkflowNotFoundException If a workflow with the given id does not exist
     */
    public function execute($id, \Serializable $parameters = null, ScheduleInterface $schedule = null, $response = null);

    /**
     * @param string $ticket
     * @return integer The percentage progress (from 0 to 100)
     */
    public function getProgress($ticket);

    /**
     * @param string $ticket
     * @return WorkflowReportInterface
     * @throws TicketNotFoundException
     */
    public function getReport($ticket);

    /**
     * @param string $ticket
     * @return Status
     * @throws TicketNotFoundException
     */
    public function getStatus($ticket);
}