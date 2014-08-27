<?php

namespace Abc\Bundle\WorkflowBundle\Doctrine;

use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\SequenceBundle\Model\SequenceManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManager as BaseExecutionManager;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Configuration;
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
    /** @var ManagerInterface */
    protected $jobManager;
    /** @var SequenceManagerInterface */
    protected $sequenceManager;

    /**
     * @param ObjectManager            $om
     * @param string                   $class
     * @param SequenceManagerInterface $sequenceManager
     */
    public function __construct(
        ObjectManager $om,
        $class,
        SequenceManagerInterface $sequenceManager)
    {
        $this->objectManager   = $om;
        $this->repository      = $om->getRepository($class);
        $this->sequenceManager = $sequenceManager;

        $metadata    = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function create($ticket, WorkflowInterface $workflow)
    {
        $execution = parent::create($ticket, $workflow);

        $executionNumber = $this->sequenceManager->getNextValue('workflow-' . $workflow->getId());
        $execution->setExecutionNumber($executionNumber);

        return $execution;
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
        if($andFlush)
        {
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
     * {@inheritDoc}
     */
    public function findHistory($workflowId, array $orderBy = array('createdAt' => 'DESC'), $limit = 20, $offset = null)
    {
        $executions = $this->findBy(
            array('workflowId' => $workflowId),
            $orderBy,
            $limit,
            $offset
        );

        foreach($executions as $key => $execution)
        {
            /** @var ExecutionInterface $execution */
            if(!in_array($execution->getStatus()->getValue(), Status::getTerminatedStatusValues()))
            {
                $report = $this->manager->getReport($execution->getTicket());
                $execution->setStatus($report->getStatus());
                $execution->setExecutionTime($report->getExecutionTime());
                $executions[$key] = $execution;
            }
        }

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
        $execution = $this->repository->find($id);

        if(!in_array($execution->getStatus()->getValue(), Status::getTerminatedStatusValues()))
        {
            $report = $this->manager->getReport($execution->getTicket());
            $execution->setStatus($report->getStatus());
            $execution->setExecutionTime($report->getExecutionTime());
        }

        return $execution;
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }
}