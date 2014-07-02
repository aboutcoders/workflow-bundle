<?php
namespace Abc\Bundle\WorkflowBundle\Model;

abstract class WorkflowExecutionManager implements WorkflowExecutionManagerInterface
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