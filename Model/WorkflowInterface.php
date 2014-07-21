<?php
namespace Abc\Bundle\WorkflowBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface WorkflowInterface
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
     * @param Task $task
     */
    public function addTask(Task $task);

    /**
     * Remove Tasks
     *
     * @param Task $task
     */
    public function removeTask(Task $task);

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
} 