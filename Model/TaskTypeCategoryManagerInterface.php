<?php

namespace Abc\Bundle\WorkflowBundle\Model;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
interface TaskTypeCategoryManagerInterface
{

    /**
     * Returns an empty TaskTypeCategory instance.
     *
     * @return TaskTypeCategoryInterface
     */
    public function create();


    /**
     * Updates a TaskTypeCategory.
     *
     * @param TaskTypeCategoryInterface $item
     * @return void
     */
    public function update(TaskTypeCategoryInterface $item);


    /**
     * Deletes a TaskTypeCategory.
     *
     * @param TaskTypeCategoryInterface $item
     * @return void
     */
    public function delete(TaskTypeCategoryInterface $item);


    /**
     * Finds a TaskTypeCategory by the given criteria.
     *
     * @param array $criteria
     * @return TaskTypeCategoryInterface
     */
    public function findBy(array $criteria);

    /**
     * Finds a TaskTypeCategory by the given id.
     *
     * @param string $id
     * @return TaskTypeCategoryInterface
     */
    public function findById($id);

    /**
     * Returns a collection with all instances.
     *
     * @return \Traversable
     */
    public function findAll();


    /**
     * Returns the TaskTypeCategory's fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}