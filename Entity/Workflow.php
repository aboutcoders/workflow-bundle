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


}
