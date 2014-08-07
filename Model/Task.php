<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 *
 * @ExclusionPolicy("all")
 */
class Task implements TaskInterface
{
    /**
     * @var int
     * @Expose
     */
    protected $id;

    /**
     * @var Workflow
     */
    protected $workflow;

    /**
     * @var string
     * @Expose
     */
    protected $description = "";

    /**
     * @var TaskType
     * @Expose
     */
    protected $type;

    /**
     * @var int
     * @Expose
     */
    protected $position;

    /**
     * @var boolean
     * @Expose
     */
    protected $disabled;

    /**
     * @var boolean
     */
    protected $scheduled;

    /** @var string */
    protected $parameters;

    /** @var ScheduleInterface */
    protected $schedule;

    /**
     * @var \DateTime
     * @SerializedName("created")
     * @Expose
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @SerializedName("lastmodified")
     * @Expose
     */
    protected $updatedAt;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function setWorkflow(WorkflowInterface $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(TaskTypeInterface $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(\Serializable $parameters = null)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setSchedule(ScheduleInterface $schedule = null)
    {
        $this->schedule = $schedule;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isScheduled()
    {
        return $this->scheduled;
    }

    /**
     * {@inheritdoc}
     */
    public function setScheduled($scheduled)
    {
        $this->scheduled = $scheduled;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    function __toString()
    {
        return $this->description;
    }
}