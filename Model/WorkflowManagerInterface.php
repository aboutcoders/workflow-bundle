<?php

namespace Abc\Bundle\WorkflowBundle\Model;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
interface WorkflowManagerInterface
{

    /**
     * Returns an empty Workflow instance.
     *
     * @return WorkflowInterface
     */
    public function create();


    /**
     * Updates a Workflow.
     *
     * @param WorkflowInterface $item
     * @return void
     */
    public function update(WorkflowInterface $item);


    /**
     * Deletes a Workflow.
     *
     * @param WorkflowInterface $item
     * @return void
     */
    public function delete(WorkflowInterface $item);


    /**
     * Finds a Workflow by the given criteria.
     *
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Finds a Workflow by the given criteria.
     *
     * @param array $criteria
     * @return WorkflowInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds a Workflow by the given id.
     *
     * @param string $id
     * @return WorkflowInterface
     */
    public function findById($id);

    /**
     * Returns a collection with all instances.
     *
     * @return \Traversable
     */
    public function findAll();


    /**
     * Returns the Workflow's fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}