<?php

namespace Abc\Bundle\WorkflowBundle\Listener;

use Abc\Bundle\JobBundle\Event\JobEvent;
use Abc\File\DistributionManagerInterface;
use Abc\File\FilesystemInterface;

class WorkflowFilesystemListener
{
    /** @var DistributionManagerInterface  */
    protected $manager;
    /** @var FilesystemInterface  */
    protected $baseFilesystem;

    /**
     * @param DistributionManagerInterface $manager
     * @param FilesystemInterface  $baseFilesystem
     */
    public function __construct(DistributionManagerInterface $manager, FilesystemInterface $baseFilesystem)
    {
        $this->manager      = $manager;
        $this->baseFilesystem = $baseFilesystem;
    }

    /**
     * Registers a filesystem location of type Abc\File\LocationInterface with the key 'filesystem'
     *
     * @param JobEvent $job
     */
    public function onJobPrepare(JobEvent $job)
    {
        if($job->getType() == 'workflow')
        {
            $job->getContext()->set('filesystem', $this->manager->createFilesystem($this->baseFilesystem, $job->getTicket()));
        }
    }
}