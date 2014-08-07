<?php
namespace Abc\Bundle\WorkflowBundle\Model;

abstract class TaskTypeCategoryManager implements TaskTypeCategoryManagerInterface
{

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $class = $this->getClass();
        $user  = new $class;

        return $user;
    }
}