<?php

namespace Abc\Bundle\WorkflowBundle\Model;

abstract class ExecutionManager implements ExecutionManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function create($ticket, WorkflowInterface $workflow)
    {
        $class = $this->getClass();

        /** @var ExecutionInterface $execution */
        $execution = new $class;

        $execution->setWorkflow($workflow);
        $execution->setTicket($ticket);

        return $execution;
    }
}