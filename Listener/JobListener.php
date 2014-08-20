<?php

namespace Abc\Bundle\WorkflowBundle\Listener;

use Abc\Bundle\JobBundle\Event\ReportEvent;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\Report\Report;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Configuration;
use Abc\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class JobListener
{
    /** @var Filesystem */
    protected $filesystem;
    /** @var LoggerInterface */
    protected $logger;
    /** @var  ExecutionManagerInterface */
    protected $executionManager;

    /**
     * @param Filesystem                $filesystem
     * @param ExecutionManagerInterface $executionManager
     * @param LoggerInterface|null      $logger
     */
    public function __construct(Filesystem $filesystem, ExecutionManagerInterface $executionManager, LoggerInterface $logger = null)
    {
        $this->filesystem       = $filesystem;
        $this->executionManager = $executionManager;
        $this->logger           = $logger == null ? new NullLogger() : $logger;
    }

    /**
     * Registers a filesystem of type Abc\Filesystem\Filesystem with the key 'filesystem'
     *
     * @param Job $job
     */
    public function onPrepare(Job $job)
    {
        if($job->getRootJob()->getType() != 'workflow')
        {
            return;
        }

        $this->logger->debug('Prepare job {ticket}', array('ticket' => $job->getTicket()));

        $configuration = $job->getRootJob()->getParameters();
        if(!$configuration instanceof Configuration)
        {
            $this->logger->error(
                'Failed to set filesystem into context for job {ticket} since job contains unexpected parameter ' . null == $configuration || !is_object(
                    $configuration
                ) ? $configuration : get_class($configuration),
                array('ticket' => $job->getTicket())
            );

            return;
        }

        if($configuration->getCreateDirectory())
        {
            try
            {
                $this->logger->debug('Add filesystem to context of job {ticket}', array('ticket' => $job->getTicket()));

                $filesystem = $this->filesystem->createFilesystem($job->getRootJob()->getTicket(), true);
                $job->getContext()->set('filesystem', $filesystem);
            }
            catch(\Exception $e)
            {
                $this->logger->error('Failed to set filesystem in context for job {ticket} ({exception})', array('ticket', $job->getTicket(), 'exception' => $e));
            }
        }
    }

    public function onTerminate(ReportEvent $event)
    {
        if($event->getReport()->getType() != 'workflow')
        {
            return;
        }

        $configuration = $event->getReport()->getParameters();

        $this->updateExecution($event->getReport());

        if(!$configuration instanceof Configuration)
        {
            $this->logger->error(
                'Failed to process report of job {ticket} since job contains unexpected parameter ' . null == $configuration || !is_object(
                    $configuration
                ) ? $configuration : get_class($configuration),
                array('ticket' => $event->getReport()->getTicket())
            );

            return;
        }

        if($configuration->getRemoveDirectory())
        {
            try
            {
                $this->logger->debug('Remove filesystem of job {ticket}', array('ticket' => $event->getReport()->getTicket()));

                $this->filesystem->remove($event->getReport()->getTicket());
            }
            catch(\Exception $e)
            {
                $this->logger->error('Failed to remove working directory for job {ticket} ({exception})', array('ticket', $event->getReport()->getTicket(), 'exception' => $e));
            }
        }
    }

    /**
     * Update execution data after finished job
     *
     * @param Report $report
     */
    protected function updateExecution(Report $report)
    {
        $execution = $this->executionManager->findOneBy(array('ticket' => $report->getTicket()));
        if($execution)
        {
            $execution->setExecutionTime($report->getExecutionTime());
            $execution->setStatus($report->getStatus());
            $this->executionManager->update($execution);
        }
    }
}