<?php

namespace Abc\Bundle\WorkflowBundle\Entity;

use Abc\Bundle\WorkflowBundle\Doctrine\TaskTypeCategoryManager as BaseTaskTypeCategoryManager;
use Doctrine\ORM\EntityManager;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class TaskTypeCategoryManager extends BaseTaskTypeCategoryManager
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