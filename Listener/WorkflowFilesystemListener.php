<?php

namespace Abc\Bundle\WorkflowBundle\Listener;

use Abc\Bundle\FileDistributionBundle\Entity\Filesystem;
use Abc\Bundle\JobBundle\Event\JobEvent;
use Abc\File\DistributionManagerInterface;
use Abc\File\FilesystemInterface;

class WorkflowFilesystemListener
{
    /** @var DistributionManagerInterface */
    protected $manager;
    /** @var FilesystemInterface */
    protected $baseFilesystem;

    /**
     * @param DistributionManagerInterface $manager
     * @param FilesystemInterface          $baseFilesystem
     */
    public function __construct(DistributionManagerInterface $manager, FilesystemInterface $baseFilesystem)
    {
        $this->manager        = $manager;
        $this->baseFilesystem = $baseFilesystem;
    }

    /**
     * Registers a filesystem location of type Abc\File\FilesystemInterface with the key 'filesystem'
     *
     * @param JobEvent $job
     */
    public function onJobPrepare(JobEvent $job)
    {
        if ($job->getType() == 'workflow' || ($job->hasParentJob() && $job->getParentJob()->getType() == 'workflow')) {
            $path = $this->baseFilesystem->getPath() . '/' . $job->getTicket();

            $filesystem = new Filesystem();
            $filesystem->setType('Filesystem');
            if (!file_exists($path)) {
                $filesystem = $this->manager->createFilesystem($filesystem, $path);
            } else {
                $filesystem->setPath($path);
            }

            $job->getContext()->set('filesystem', $filesystem);
        }
    }
}