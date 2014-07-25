<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\SchedulerBundle\Model\Schedule as BaseSchedule;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class Schedule extends BaseSchedule implements ScheduleInterface
{
    /** @var string */
    protected $type = 'cron';

    /** @var \DateTime */
    protected $createdAt;

    /** @var \DateTime */
    protected $updatedAt;

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function __clone()
    {
        parent::__clone();

        $this->createdAt = null;
        $this->updatedAt = null;
    }
}