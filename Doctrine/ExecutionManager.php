<?php

namespace Abc\Bundle\WorkflowBundle\Doctrine;

use Abc\Bundle\WorkflowBundle\Model\ExecutionInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManager as BaseExecutionManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class ExecutionManager extends BaseExecutionManager
{
    /** @var ObjectManager */
    protected $objectManager;
    /** @var string */
    protected $class;
    /** @var ObjectRepository */
    protected $repository;


    /**
     * @param ObjectManager $om
     * @param string        $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository    = $om->getRepository($class);

        $metadata    = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param ExecutionInterface $item
     * @param Boolean            $andFlush Whether to flush the changes (default true)
     */
    public function update(ExecutionInterface $item, $andFlush = true)
    {
        $this->objectManager->persist($item);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(ExecutionInterface $item)
    {
        $this->objectManager->remove($item);
        $this->objectManager->flush();
    }


    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds workflow history with ticket details
     *
     * @param int      $workflowId
     * @param array    $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findHistory($workflowId, array $orderBy = array('createdAt' => 'DESC'), $limit = 20, $offset = null)
    {
        $executions = $this->findBy(
            array('workflowId' => $workflowId),
            $orderBy,
            $limit,
            $offset
        );

        return $executions;
    }


    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }
}