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
class Workflow implements WorkflowInterface, \Serializable
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

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param boolean $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * Get all tasks
     *
     * @return ArrayCollection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Add Task
     *
     * @param Task $task
     * @return Workflow
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove Tasks
     *
     * @param Task $task
     */
    public function removeTask(Task $task)
    {
        $this->tasks->removeElement($task);
    }


    /**
     * Get all workflow executions
     *
     * @return ArrayCollection
     */
    public function getExecutions()
    {
        return $this->tasks;
    }

    /**
     * Add WorkflowExecution
     *
     * @param WorkflowExecutionInterface $workflowExecution
     * @return Workflow
     */
    public function addExecution(WorkflowExecutionInterface $workflowExecution)
    {
        $this->executions[] = $workflowExecution;

        return $this;
    }

    /**
     * Remove WorkflowExecutions
     *
     * @param WorkflowExecutionInterface $workflowExecution
     */
    public function removeExecution(WorkflowExecutionInterface $workflowExecution)
    {
        $this->executions->removeElement($workflowExecution);
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
        return $this->name;
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