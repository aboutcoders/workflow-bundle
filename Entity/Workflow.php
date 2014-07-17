<?php
namespace Abc\Bundle\WorkflowBundle\Entity;

use Abc\Bundle\WorkflowBundle\Model\Workflow as BaseWorkflow;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class Workflow extends BaseWorkflow
{
    /** @var int */
    protected $index = 0;
    /** @var \Serializable */
    protected $parameters;

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param int $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }


    /**
     * @return \Serializable
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param \Serializable $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
