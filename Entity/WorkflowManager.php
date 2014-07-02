<?php

namespace Abc\Bundle\WorkflowBundle\Entity;

use Abc\Bundle\WorkflowBundle\Doctrine\WorkflowManager as BaseWorkflowManager;
use Doctrine\ORM\EntityManager;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class WorkflowManager extends BaseWorkflowManager
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