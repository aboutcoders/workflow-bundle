<?php

namespace Abc\Bundle\WorkflowBundle\Listener;

use Abc\Bundle\JobBundle\Event\ExecutionEvent;
use Abc\Bundle\JobBundle\Event\JobEvents;
use Abc\Bundle\JobBundle\Event\ReportEvent;
use Abc\Bundle\JobBundle\Event\TerminationEvent;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\JobInterface;
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
    public function onPrepare(ExecutionEvent $event)
    {
        if ($event->getJob()->getType() != 'workflow') {
            return;
        }

        $this->logger->debug('Handle event {name}', ['name' => JobEvents::JOB_PRE_EXECUTE]);

        $configuration = $event->getJob()->getParameters()[0];
        $this->logger->debug('Check workflow Configuration for {ticket}', ['ticket' => $event->getJob()->getTicket()]);
        if (!$configuration instanceof Configuration) {
            $this->logger->error(
                'Failed to set filesystem into context for job {ticket} since job contains unexpected parameter ' . null == $configuration || !is_object(
                    $configuration
                ) ? $configuration : get_class($configuration),
                ['ticket' => $event->getTicket()]
            );

            return;
        }

        if ($configuration->getCreateDirectory()) {
            try {
                $this->logger->debug('Add filesystem to context of job {ticket}', ['ticket' => $event->getJob()->getTicket()]);

                $filesystem = $this->filesystem->createDir($event->getJob()->getTicket());
                $event->getContext()->set('filesystem', $filesystem);
            } catch (\Exception $e) {
                $this->logger->error('Failed to set filesystem in context for job {ticket} ({exception})',
                    ['ticket', $event->getTicket(), 'exception' => $e]);
            }
        }
    }

    public function onTerminated(TerminationEvent $event)
    {
        if ($event->getJob()->getType() != 'workflow') {
            return;
        }

        $this->logger->debug('Handle event {name}', ['name' => JobEvents::JOB_TERMINATED]);

        $configuration = $event->getJob()->getParameters()[0];

        $this->updateExecution($event->getJob());

        if (!$configuration instanceof Configuration) {
            $this->logger->error(
                'Failed to process report of job {ticket} since job contains unexpected parameter ' . null == $configuration || !is_object(
                    $configuration
                ) ? $configuration : get_class($configuration),
                ['ticket' => $event->getJob()->getTicket()]
            );

            return;
        }

        if ($configuration->getRemoveDirectory() && $this->filesystem->exists($event->getJob()->getTicket())) {
            try {
                $this->logger->debug('Remove {path} from filesystem', ['path' => $event->getJob()->getTicket()]);

                $this->filesystem->remove($event->getJob()->getTicket());
            } catch (\Exception $e) {
                $this->logger->error('Failed to remove working directory for job {ticket} ({exception})',
                    ['ticket', $event->getJob()->getTicket(), 'exception' => $e]);
            }
        } else {
            $this->logger->debug('Removal of filesystem for job {ticket} is disabled', ['ticket' => $event->getReport()->getTicket()]);
        }
    }

    public function onPostExecute(ExecutionEvent $event)
    {
        if ($event->getJob()->getType() != 'workflow') {
            return;
        }

        $this->logger->debug('Handle event {name}', ['name' => JobEvents::JOB_POST_EXECUTE]);

        $this->updateExecution($event->getJob());
    }

    /**
     * Update execution data after finished job
     *
     * @param Job $job
     */
    protected function updateExecution(JobInterface $job)
    {
        $execution = $this->executionManager->findOneBy(['ticket' => $job->getTicket()]);
        if ($execution) {
            $execution->setExecutionTime($job->getExecutionTime());
            $execution->setStatus($job->getStatus());
            $this->executionManager->update($execution);
        }
    }
}