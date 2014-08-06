<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface WorkflowInterface extends \Serializable
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return boolean
     */
    public function isDisabled();

    /**
     * @param boolean $disabled
     */
    public function setDisabled($disabled);

    /**
     * Get all tasks
     *
     * @return ArrayCollection
     */
    public function getTasks();

    /**
     * Add Task
     *
     * @param TaskInterface $task
     */
    public function addTask(TaskInterface $task);

    /**
     * Remove Tasks
     *
     * @param TaskInterface $task
     */
    public function removeTask(TaskInterface $task);

    /**
     * Get all workflow executions
     *
     * @return ArrayCollection
     */
    public function getExecutions();

    /**
     * @param ExecutionInterface $execution
     * @return Workflow
     */
    public function addExecution(ExecutionInterface $execution);

    /**
     * @param ExecutionInterface $execution
     */
    public function removeExecution(ExecutionInterface $execution);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get the index of the currently executed task (must be serializable)
     *
     * @return int
     */
    public function getIndex();

    /**
     * Set the index of the currently executed task (must be serializable)
     *
     * @param int $index
     */
    public function setIndex($index);

    /**
     * Get parameters of this workflow (must be serializable)
     *
     * @return \Serializable|null
     */
    public function getParameters();

    /**
     * Set parameters of this workflow (must be serializable)
     *
     * @param \Serializable|null $parameters
     */
    public function setParameters(\Serializable $parameters = null);
}