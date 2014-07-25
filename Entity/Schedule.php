<?php

namespace Abc\Bundle\WorkflowBundle\Entity;

use Abc\Bundle\WorkflowBundle\Model\Schedule as BaseSchedule;

class Schedule extends BaseSchedule
{
    /** @var integer */
    protected $id;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}