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
     * @return void
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return void
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description);

    /**
     * @return boolean
     */
    public function isDisabled();

    /**
     * @param boolean $disabled
     * @return void
     */
    public function setDisabled($disabled);

    /**
     * Whether to create a working directory for the execution
     *
     * @return boolean
     */
    public function getCreateDirectory();

    /**
     * Set whether to create a working directory for the execution
     *
     * @param boolean $createDirectory
     * @return void
     */
    public function setCreateDirectory($createDirectory);

    /**
     * Whether to destroy the working directory after the execution
     *
     * @return boolean
     */
    public function getRemoveDirectory();

    /**
     * Set whether to remove the working directory after the execution
     *
     * @param boolean $removeDirectory
     * @return void
     */
    public function setRemoveDirectory($removeDirectory);

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
     * @return void
     */
    public function addTask(TaskInterface $task);

    /**
     * Remove Tasks
     *
     * @param TaskInterface $task
     * @return void
     */
    public function removeTask(TaskInterface $task);

    /**
     * Get all executions
     *
     * @return ArrayCollection
     */
    public function getExecutions();

    /**
     * @param ExecutionInterface $execution
     * @return WorkflowInterface
     * @return void
     */
    public function addExecution(ExecutionInterface $execution);

    /**
     * @param ExecutionInterface $execution
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
     */
    public function setParameters(\Serializable $parameters = null);

    /**
     * @return TaskTypeCategory
     */
    public function getCategory();

    /**
     * @param TaskTypeCategoryInterface $category
     * @return void
     */
    public function setCategory(TaskTypeCategoryInterface $category);
}