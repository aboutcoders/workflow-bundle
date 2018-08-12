<?php

namespace Abc\Bundle\WorkflowBundle\Doctrine;

use Abc\Bundle\WorkflowBundle\Model\WorkflowManager as BaseWorkflowManager;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class WorkflowManager extends BaseWorkflowManager
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
     * Updates a Workflow
     *
     * @param WorkflowInterface $item
     * @param Boolean           $andFlush Whether to flush the changes (default true)
     */
    public function update(WorkflowInterface $item, $andFlush = true)
    {
        $this->objectManager->persist($item);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(WorkflowInterface $item)
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

    /**
     * @inheritdoc
     */
    public function findByCount(array $criteria)
    {
        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder->select(sprintf('COUNT(%s)', $this->getAlias()));

        $queryBuilder = $this->buildMatchingQueryForCriteria($queryBuilder, $criteria);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return $this->objectManager->getRepository($this->getClass())->createQueryBuilder($this->getAlias());
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array                      $criteria
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function buildMatchingQueryForCriteria($queryBuilder, array $criteria)
    {
        foreach ($criteria as $key => $value) {

            $operator = ' = :%s';

            if (null === $value) {
                $queryBuilder->andWhere($this->getAlias() . '.' . $key . ' IS NULL');
            } else {
                if (is_array($value)) {

                    if (count($value) == 1 && array_keys($value)[0] === '$match') {

                        $firstValue = reset($value);

                        //Only like is supported here at the moment
                        $operator = ' LIKE :%s';
                        $value    = '%' . $firstValue . '%';

                    } else {
                        $operator = ' IN (:%s)';
                    }
                }

                $queryBuilder->andWhere($this->getAlias() . '.' . $key . sprintf($operator, $key))
                    ->setParameter($key, $value);
            }
        }

        return $queryBuilder;
    }

    /**
     * @return string
     */
    protected function getAlias()
    {
        return 'workflow';
    }
}