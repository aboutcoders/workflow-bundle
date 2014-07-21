<?php

namespace Abc\Bundle\WorkflowBundle\Model;

abstract class ExecutionManager implements ExecutionManagerInterface
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