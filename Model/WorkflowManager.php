<?php
namespace Abc\Bundle\WorkflowBundle\Model;

abstract class WorkflowManager implements WorkflowManagerInterface
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