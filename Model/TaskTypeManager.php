<?php
namespace Abc\Bundle\WorkflowBundle\Model;

abstract class TaskTypeManager implements TaskTypeManagerInterface
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