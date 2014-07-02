<?php

namespace Abc\Bundle\WorkflowBundle\Model;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
interface TaskTypeManagerInterface
{

    /**
     * Returns an empty TaskType instance.
     *
     * @return TaskTypeInterface
     */
    public function create();


    /**
     * Updates a TaskType.
     *
     * @param TaskTypeInterface $item
     * @return void
     */
    public function update(TaskTypeInterface $item);


    /**
     * Deletes a TaskType.
     *
     * @param TaskTypeInterface $item
     * @return void
     */
    public function delete(TaskTypeInterface $item);


    /**
     * Finds a TaskType by the given criteria.
     *
     * @param array $criteria
     * @return TaskTypeInterface
     */
    public function findBy(array $criteria);

    /**
     * Finds a TaskType by the given id.
     *
     * @param string $id
     * @return TaskTypeInterface
     */
    public function findById($id);

    /**
     * Returns a collection with all instances.
     *
     * @return \Traversable
     */
    public function findAll();


    /**
     * Returns the TaskType's fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}