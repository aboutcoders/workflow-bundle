<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\SchedulerBundle\Model\ScheduleInterface as BaseScheduleInterface;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
interface ScheduleInterface extends BaseScheduleInterface
{
    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
} 