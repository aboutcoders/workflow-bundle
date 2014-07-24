<?php

namespace Abc\Bundle\WorkflowBundle\Listener;

use Abc\Bundle\JobBundle\Job\Job;
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
     * @param Job $job
     */
    public function onJobPrepare(Job $job)
    {
        if($job->getRootJob()->getType() == 'workflow')
        {
            $filesystem = $this->filesystem->createFilesystem($job->getTicket(), true);

            $job->getContext()->set('filesystem', $filesystem);
        }
    }
}