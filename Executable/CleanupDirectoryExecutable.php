<?php

namespace Abc\Bundle\WorkflowBundle\Executable;

use Abc\Bundle\JobBundle\Job\Executable;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\WorkflowBundle\Workflow\CleanupDirectoryConfiguration;
use League\Flysystem\MountManager;

class CleanupDirectoryExecutable implements Executable
{
    /** @var MountManager */
    protected $mountManager;

    public function __construct(MountManager $mountManager)
    {
        $this->mountManager = $mountManager;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Job $job)
    {
        if (!$job->getParameters() instanceof CleanupDirectoryConfiguration) {
            throw new \InvalidArgumentException('Parameters must be an instance of Abc\Bundle\WorkflowBundle\Workflow\CleanupDirectoryConfiguration');
        }

        $logger = $job->getContext()->get('logger');

        /** @var CleanupDirectoryConfiguration $parameters */
        $parameters = $job->getParameters();
        $filesystem = $this->mountManager->create($parameters->getFilesystemDefinition());

        $logger->debug('Remove directory {path}', array('path' => $parameters->getPath()));
        $filesystem->remove('/');

        $logger->debug('Remove schedule');
        $job->removeSchedule();
    }
} 