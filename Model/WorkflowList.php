<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use JMS\Serializer\Annotation as JMS;

class WorkflowList
{
    /**
     * @JMS\Type("array<Abc\Bundle\WorkflowBundle\Model\Workflow>")
     */
    protected $items;

    /**
     * @JMS\Type("integer")
     * @JMS\SerializedName("totalCount")
     * @var int
     */
    protected $totalCount;

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalTotalCount
     */
    public function setTotalCount($totalTotalCount)
    {
        $this->totalCount = $totalTotalCount;
    }
}