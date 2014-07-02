<?php
namespace Abc\Bundle\WorkflowBundle\Model;

abstract class TaskManager implements TaskManagerInterface
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