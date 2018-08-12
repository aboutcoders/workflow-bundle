<?php

namespace Abc\Bundle\WorkflowBundle\Listener;

use Abc\Bundle\JobBundle\Event\JobEvent;
use Abc\Bundle\JobBundle\Event\JobEvents;
use Abc\Bundle\JobBundle\Event\ReportEvent;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\Report\Report;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Configuration;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class JobListener
{
    /** @var FilesystemInterface */
    protected $filesystem;
    /** @var LoggerInterface */
    protected $logger;
    /** @var  ExecutionManagerInterface */
    protected $executionManager;

    /**
     * @param FilesystemInterface       $filesystem
     * @param ExecutionManagerInterface $executionManager
     * @param LoggerInterface|null      $logger
     */
    public function __construct(FilesystemInterface $filesystem, ExecutionManagerInterface $executionManager, LoggerInterface $logger = null)
    {
        $this->filesystem       = $filesystem;
        $this->executionManager = $executionManager;
        $this->logger           = $logger == null ? new NullLogger() : $logger;
    }

    /**
     * Registers a filesystem of type Abc\Filesystem\Filesystem with the key 'filesystem'
     *
     * @param JobEvent $event
     */
    public function onPrepare(JobEvent $event)
    {
        if($event->getRootJob()->getType() != 'workflow')
        {
            return;
        }

        $this->logger->debug('Handle event {name}', array('name' => JobEvents::JOB_PREPARE));

        $configuration = $event->getRootJob()->getParameters();
        if(!$configuration instanceof Configuration)
        {
            $this->logger->error(
                'Failed to set filesystem into context for job {ticket} since job contains unexpected parameter ' . null == $configuration || !is_object(
                    $configuration
                ) ? $configuration : get_class($configuration),
                array('ticket' => $event->getTicket())
            );

            return;
        }

        if($configuration->getCreateDirectory())
        {
            try
            {
                $this->logger->debug('Add filesystem to context of job {ticket}', array('ticket' => $event->getTicket()));

                $filesystem = $this->filesystem->createFilesystem($event->getRootJob()->getTicket(), true);
                $event->getContext()->set('filesystem', $filesystem);
            }
            catch(\Exception $e)
            {
                $this->logger->error('Failed to set filesystem in context for job {ticket} ({exception})', array('ticket', $event->getTicket(), 'exception' => $e));
            }
        }
    }

    public function onTerminated(ReportEvent $event)
    {
        if($event->getReport()->getType() != 'workflow')
        {
            return;
        }

        $this->logger->debug('Handle event {name}', array('name' => JobEvents::JOB_TERMINATED));

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

        if($configuration->getRemoveDirectory() && $this->filesystem->exists($event->getReport()->getTicket()))
        {
            try
            {
                $this->logger->debug('Remove {path} from filesystem', array('path' => $event->getReport()->getTicket()));

                $this->filesystem->remove($event->getReport()->getTicket());
            }
            catch(\Exception $e)
            {
                $this->logger->error('Failed to remove working directory for job {ticket} ({exception})', array('ticket', $event->getReport()->getTicket(), 'exception' => $e));
            }
        }
        else
        {
            $this->logger->debug('Removal of filesystem for job {ticket} is disabled', array('ticket' => $event->getReport()->getTicket()));
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