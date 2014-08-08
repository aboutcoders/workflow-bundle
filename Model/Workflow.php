<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 *
 * @ExclusionPolicy("all")
 */
class Workflow implements WorkflowInterface
{
    /**
     * @var int
     * @Expose
     */
    protected $id;

    /**
     * @var string
     * @Expose
     */
    protected $name;

    /**
     * @var string
     * @Expose
     */
    protected $description;

    /**
     * @var boolean
     * @Expose
     */
    protected $disabled;

    /**
     * @var ArrayCollection
     * @SerializedName("tasks")
     * @Expose
     */
    protected $tasks;

    /**
     * @var ArrayCollection
     */
    protected $executions;

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

    /** @var int */
    protected $index = 0;
    /** @var \Serializable */
    protected $parameters;

    /**
     * @var TaskTypeCategoryInterface
     */
    protected $category;

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritDoc}
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * {@inheritDoc}
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * {@inheritDoc}
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * {@inheritDoc}
     */
    public function addTask(TaskInterface $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeTask(TaskInterface $task)
    {
        $this->tasks->removeElement($task);
    }


    /**
     * {@inheritDoc}
     */
    public function getExecutions()
    {
        return $this->tasks;
    }

    /**
     * {@inheritDoc}
     */
    public function addExecution(ExecutionInterface $execution)
    {
        $this->executions[] = $execution;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeExecution(ExecutionInterface $execution)
    {
        $this->executions->removeElement($execution);
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritDoc}
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
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritDoc}
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function setParameters(\Serializable $parameters = null)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategory(TaskTypeCategoryInterface $category)
    {
        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $attributes = get_object_vars($this);
        return serialize($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data       = unserialize($serialized);
        $attributes = get_object_vars($this);
        foreach ($attributes as $key => $attribute) {
            $this->$key = $data[$key];
        }
    }
}