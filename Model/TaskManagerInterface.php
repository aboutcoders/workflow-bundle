<?php

namespace Abc\Bundle\WorkflowBundle\Model;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
interface TaskManagerInterface
{

    /**
     * Returns an empty Task instance.
     *
     * @return TaskInterface
     */
    public function create();


    /**
     * Updates a Task.
     *
     * @param TaskInterface $item
     * @return void
     */
    public function update(TaskInterface $item);


    /**
     * Deletes a Task.
     *
     * @param TaskInterface $item
     * @return void
     */
    public function delete(TaskInterface $item);


    /**
     * Finds a Task by the given criteria.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null  $limit
     * @param null  $offset
     * @return TaskInterface
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Finds Tasks by the given workflow id.
     *
     * @param int $workflowId
     * @return \Traversable
     */
    public function findWorkflowTasks($workflowId);

    /**
     * Finds next task Task to execute by the given workflow id.
     *
     * @param int $workflowId
     * @param int $index
     * @return TaskInterface
     */
    public function findNextWorkflowTask($workflowId, $index = 0);

    /**
     * Finds a Task by the given id.
     *
     * @param string $id
     * @return TaskInterface
     */
    public function findById($id);

    /**
     * Returns a collection with all instances.
     *
     * @return \Traversable
     */
    public function findAll();


    /**
     * Returns the Task's fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}