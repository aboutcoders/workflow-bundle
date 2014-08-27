<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\WorkflowBundle\Workflow\ManagerInterface;

abstract class ExecutionManager implements ExecutionManagerInterface
{
    /** @var ManagerInterface */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function setManager(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

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