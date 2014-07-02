<?php
namespace Abc\Bundle\WorkflowBundle\Entity;

use Abc\Bundle\WorkflowBundle\Model\WorkflowExecution as BaseWorkflowExecution;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class WorkflowExecution extends BaseWorkflowExecution
{
    /** @var int */
    protected $workflowId;

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

}
