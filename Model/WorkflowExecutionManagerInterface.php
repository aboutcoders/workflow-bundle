<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\JobBundle\Api\Manager;
use Abc\Bundle\JobBundle\Model\JobManager;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
interface WorkflowExecutionManagerInterface
{

    /**
     * Returns an empty WorkflowExecution instance.
     *
     * @return WorkflowExecutionInterface
     */
    public function create();


    /**
     * Updates a WorkflowExecution.
     *
     * @param WorkflowExecutionInterface $item
     * @return void
     */
    public function update(WorkflowExecutionInterface $item);


    /**
     * Deletes a WorkflowExecution.
     *
     * @param WorkflowExecutionInterface $item
     * @return void
     */
    public function delete(WorkflowExecutionInterface $item);


    /**
     * Finds a WorkflowExecution by the given criteria.
     *
     * @param array    $criteria
     * @param array    $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Finds a WorkflowExecution by the given criteria.
     *
     * @param array $criteria
     * @return WorkflowExecutionInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds a WorkflowExecution by the given id.
     *
     * @param string $id
     * @return WorkflowExecutionInterface
     */
    public function findById($id);

    /**
     * Returns a collection with all instances.
     *
     * @return \Traversable
     */
    public function findAll();

    /**
     * Execute workflow
     *
     * @param Workflow      $workflow
     * @param Manager       $jobManager
     * @param \Serializable $parameters
     * @return WorkflowExecutionInterface
     */
    public function execute(Workflow $workflow, Manager $jobManager, \Serializable $parameters = null);

    /**
     * Returns the WorkflowExecution's fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}