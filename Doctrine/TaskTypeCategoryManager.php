<?php

namespace Abc\Bundle\WorkflowBundle\Doctrine;

use Abc\Bundle\WorkflowBundle\Model\TaskTypeCategoryInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeCategoryManager as BaseTaskTypeCategoryManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class TaskTypeCategoryManager extends BaseTaskTypeCategoryManager
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
     * Updates a TaskTypeCategory
     *
     * @param TaskTypeCategoryInterface $item
     * @param Boolean                   $andFlush Whether to flush the changes (default true)
     */
    public function update(TaskTypeCategoryInterface $item, $andFlush = true)
    {
        $this->objectManager->persist($item);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(TaskTypeCategoryInterface $item)
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
    public function findById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug($slug)
    {
        return $this->repository->findOneBy(array('slug' => $slug));
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }
}