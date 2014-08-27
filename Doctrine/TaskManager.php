<?php

namespace Abc\Bundle\WorkflowBundle\Doctrine;

use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\WorkflowBundle\Model\TaskInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManager as BaseTaskManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class TaskManager extends BaseTaskManager
{
    /** @var ObjectManager */
    protected $objectManager;
    /** @var string */
    protected $class;
    /** @var EntityRepository */
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
     * Updates a Task
     *
     * @param TaskInterface $item
     * @param Boolean       $andFlush Whether to flush the changes (default true)
     */
    public function update(TaskInterface $item, $andFlush = true)
    {
        // dirty hack to make changes in serializables actually being persisted
        if ($item->getParameters() != null) {
            $item->setParameters(clone $item->getParameters());
        }

        $this->objectManager->persist($item);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(TaskInterface $item)
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
     * {@inheritDoc}
     */
    public function findWorkflowTasks($id)
    {
        return $this->repository->findBy(array('workflowId' => $id), array('position' => 'ASC'));
    }

    /**
     * {@inheritDoc}
     */
    public function findNextWorkflowTask($workflowId, $index = 0)
    {
        /** @var Query $query */
        $query = $this->repository->createQueryBuilder('t')
            ->where('t.workflowId = :workflowId AND t.disabled = :disabled')
            ->setParameter('workflowId', $workflowId)
            ->setParameter('disabled', false)
            ->orderBy('t.position', 'ASC')
            ->setFirstResult($index)
            ->getQuery();

        $result = $query->getResult();

        return isset($result[0]) ? $result[0] : null;
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