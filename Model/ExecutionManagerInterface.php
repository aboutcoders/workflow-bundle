<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\JobBundle\Job\Report\ReportInterface;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
interface ExecutionManagerInterface
{

    /**
     * @param string            $ticket
     * @param WorkflowInterface $workflow
     * @return ExecutionInterface
     */
    public function create($ticket, WorkflowInterface $workflow);


    /**
     * @param string            $ticket
     * @param WorkflowInterface $workflow
     * @return ExecutionInterface
     */
    public function execute($ticket, WorkflowInterface $workflow);


    /**
     * @param ExecutionInterface $item
     * @return void
     */
    public function update(ExecutionInterface $item);


    /**
     * Get current execution progress
     *
     * @param string $ticket
     * @return integer
     */
    public function getProgress($ticket);

    /**
     * @param ExecutionInterface $item
     * @return void
     */
    public function delete(ExecutionInterface $item);


    /**
     * @param array    $criteria
     * @param array    $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Finds a entity by the given criteria.
     *
     * @param array $criteria
     * @return ExecutionInterface
     */
    public function findOneBy(array $criteria);

    /**
     * @param string $id
     * @return ExecutionInterface
     */
    public function findById($id);

    /**
     * Returns a collection with all instances.
     *
     * @return \Traversable
     */
    public function findAll();

    /**
     * Finds workflow history with ticket details
     *
     * @param int      $workflowId
     * @param array    $orderBy Order, default: createdAt DESC
     * @param int|null $limit   Limit, default: 20
     * @param int|null $offset
     * @return array
     */
    public function findHistory($workflowId, array $orderBy = array('createdAt' => 'DESC'), $limit = 20, $offset = null);

    /**
     * Returns the entity's fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}