<?php

namespace Abc\Bundle\WorkflowBundle\Listener;

use Abc\Bundle\JobBundle\Event\JobEvent;
use Abc\Filesystem\Filesystem;

class WorkflowFilesystemListener
{
    /** @var Filesystem */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Registers a filesystem of type Abc\Filesystem\Filesystem with the key 'filesystem'
     *
     * @param JobEvent $job
     */
    public function onJobPrepare(JobEvent $job)
    {
        if($job->getType() == 'workflow' || ($job->hasParentJob() && $job->getParentJob()->getType() == 'workflow'))
        {
            $filesystem = $this->filesystem->createFilesystem($job->getTicket(), true);

            $job->getContext()->set('filesystem', $filesystem);
        }
    }
}