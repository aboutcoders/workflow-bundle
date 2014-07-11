<?php

namespace Abc\Bundle\WorkflowBundle\Doctrine;

use Abc\Bundle\WorkflowBundle\Model\Workflow;
use Abc\Bundle\WorkflowBundle\Model\WorkflowExecutionInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowExecutionManager as BaseWorkflowExecutionManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Abc\Bundle\JobBundle\Api\Manager;
use Abc\Bundle\JobBundle\Model\JobManager;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
class WorkflowExecutionManager extends BaseWorkflowExecutionManager
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
     * Updates a WorkflowExecution
     *
     * @param WorkflowExecutionInterface $item
     * @param Boolean                    $andFlush Whether to flush the changes (default true)
     */
    public function update(WorkflowExecutionInterface $item, $andFlush = true)
    {
        $this->objectManager->persist($item);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }


    /**
     * {@inheritDoc}
     */
    public function delete(WorkflowExecutionInterface $item)
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
     * {@inheritDoc}
     */
    public function execute(Workflow $workflow, Manager $jobManager, JobManager $jobEntityManager, \Serializable $parameters = null)
    {
        $execution = $this->create();

        $ticket = $jobManager->addJob('workflow', $workflow);

        $job = $jobEntityManager->findById($ticket);

        $execution->setWorkflow($workflow);
        $execution->setJob($job);

        $this->update($execution);

        return $execution;
    }


}