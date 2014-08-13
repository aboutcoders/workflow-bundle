<?php

namespace Abc\Bundle\WorkflowBundle\Entity;

use Abc\Bundle\JobBundle\Job\ManagerInterface;
use Abc\Bundle\SequenceBundle\Model\SequenceManagerInterface;
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
     * @param EntityManager            $em
     * @param string                   $class
     * @param ManagerInterface         $jobManager
     * @param SequenceManagerInterface $sequenceManager
     */
    public function __construct(
        EntityManager $em,
        $class,
        ManagerInterface $jobManager,
        SequenceManagerInterface $sequenceManager)
    {
        parent::__construct($em, $class, $jobManager, $sequenceManager);
        $this->em = $em;
    }
}