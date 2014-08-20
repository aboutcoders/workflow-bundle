<?php

namespace Abc\Bundle\WorkflowBundle\Workflow;

use Abc\Bundle\SchedulerBundle\Model\ScheduleInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Exception\WorkflowNotFoundException;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
interface ManagerInterface
{

    /**
     * @param int               $id The id of the workflow
     * @param \Serializable     $parameters
     * @param ScheduleInterface $schedule
     * @param mixed|null        $response
     * @return ExecutionInterface
     * @throws WorkflowNotFoundException If a workflow with the given id does not exist
     */
    public function execute($id, \Serializable $parameters = null, ScheduleInterface $schedule = null, $response = null);
} 