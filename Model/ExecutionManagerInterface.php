<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\JobBundle\Api\Manager;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
interface ExecutionManagerInterface
{

    /**
     * @return ExecutionInterface
     */
    public function create();


    /**
     * @param ExecutionInterface $item
     * @return void
     */
    public function update(ExecutionInterface $item);


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
     * Returns the entity's fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}