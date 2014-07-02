<?php
namespace Abc\Bundle\WorkflowBundle\Entity;

use Abc\Bundle\WorkflowBundle\Model\Task as BaseTask;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class Task extends BaseTask
{
    /** @var int */
    protected $workflowId;
    /** @var int */
    protected $typeId;

    /**
     * @return int
     */
    public function getWorkflowId()
    {
        return $this->workflowId;
    }

    /**
     * @param int $workflowId
     */
    public function setWorkflowId($workflowId)
    {
        $this->workflowId = $workflowId;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }


}
