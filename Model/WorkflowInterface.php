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
     * Add WorkflowExecution
     *
     * @param WorkflowExecutionInterface $workflowExecution
     * @return Workflow
     */
    public function addExecution(WorkflowExecutionInterface $workflowExecution);

    /**
     * Remove WorkflowExecutions
     *
     * @param WorkflowExecutionInterface $workflowExecution
     */
    public function removeExecution(WorkflowExecutionInterface $workflowExecution);
} 