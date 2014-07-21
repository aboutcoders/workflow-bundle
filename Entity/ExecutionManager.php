<?php

namespace Abc\Bundle\WorkflowBundle\Entity;

use Abc\Bundle\WorkflowBundle\Doctrine\ExecutionManager as BaseExecutionManager;
use Doctrine\ORM\EntityManager;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
abstract class ExecutionManager extends BaseExecutionManager
{
    /** @var EntityManager */
    protected $em;


    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        parent::__construct($em, $class);
        $this->em = $em;
    }
}