<?php

namespace Abc\Bundle\WorkflowBundle\Listener;

use Abc\Bundle\JobBundle\Event\ReportEvent;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
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

    /**
     * @param Filesystem           $filesystem
     * @param LoggerInterface|null $logger
     */
    public function __construct(Filesystem $filesystem, LoggerInterface $logger = null)
    {
        $this->filesystem = $filesystem;
        $this->logger     = $logger == null ? new NullLogger() : $logger;
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

        $workflow = $job->getRootJob()->getParameters();
        if(!$workflow instanceof WorkflowInterface)
        {
            $this->logger->error(
                'Failed to set filesystem into context for job {ticket} since job contains unexpected parameter ' . null == $workflow || !is_object(
                    $workflow
                ) ? $workflow : get_class($workflow),
                array('ticket' => $job->getTicket())
            );

            return;
        }

        if($workflow->getCreateDirectory())
        {
            try
            {
                $filesystem = $this->filesystem->createFilesystem($job->getRootJob()->getTicket(), true);
                $job->getContext()->set('filesystem', $filesystem);
            }
            catch(\Exception $e)
            {
                $this->logger->error('Failed to set filesystem in context for job {ticket} ({exception})', array('ticket', $job->getTicket(), 'exception' => $e));
            }
        }
    }

    public function onReport(ReportEvent $event)
    {
        if($event->getReport()->getType() != 'workflow')
        {
            return;
        }

        $workflow = $event->getReport()->getParameters();

        if(!$workflow instanceof WorkflowInterface)
        {
            $this->logger->error(
                'Failed to process report of job {ticket} since job contains unexpected parameter ' . null == $workflow || !is_object(
                    $workflow
                ) ? $workflow : get_class($workflow),
                array('ticket' => $event->getReport()->getTicket())
            );

            return;
        }

        if($workflow->getRemoveDirectory())
        {
            try
            {
                $this->filesystem->remove($event->getReport()->getTicket());
            }
            catch(\Exception $e)
            {
                $this->logger->error('Failed to remove working directory for job {ticket} ({exception})', array('ticket', $event->getReport()->getTicket(), 'exception' => $e));
            }
        }
    }
}