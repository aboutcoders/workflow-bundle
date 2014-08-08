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
        /** @var WorkflowInterface $workflow */
        $workflow = new $class;
        $workflow->setCreateDirectory(true);
        $workflow->setRemoveDirectory(true);

        return $workflow;
    }
}