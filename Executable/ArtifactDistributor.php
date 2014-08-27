<?php

namespace Abc\Bundle\WorkflowBundle\Executable;

use Abc\Bundle\JobBundle\Job\Executable;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\ManagerInterface as JobManagerInterface;
use Abc\Bundle\FileDistributionBundle\Model\DefinitionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\Schedule;
use Abc\Bundle\WorkflowBundle\Model\ScheduleInterface;
use Abc\Bundle\WorkflowBundle\Workflow\CleanupDirectoryConfiguration;
use Abc\Filesystem\File;
use Abc\Filesystem\FilesystemFactoryInterface;
use Abc\Filesystem\FilesystemInterface;

class ArtifactDistributor implements Executable
{
    /** @var DefinitionManagerInterface */
    protected $definitionManager;
    /** @var FilesystemFactoryInterface */
    protected $filesystemFactory;
    /** @var JobManagerInterface */
    protected $jobManager;

    function __construct(
        DefinitionManagerInterface $definitionManager,
        FilesystemFactoryInterface $filesystemFactory,
        JobManagerInterface $jobManager)
    {
        $this->definitionManager = $definitionManager;
        $this->filesystemFactory = $filesystemFactory;
        $this->jobManager        = $jobManager;
    }


    /**
     * {@inheritDoc}
     */
    public function execute(Job $job)
    {
        $logger = $job->getContext()->get('logger');

        /** @var DistributeArtifactsParameter $parameters */
        $parameters   = $job->getParameters();
        $definitionId = $parameters->getDefinitionId();

        if ($definitionId == null)
        {
            throw new \InvalidArgumentException('Target Filesystem definition is not defined');
        }

        $logger->debug('Get artifacts from working directory');
        /** @var FilesystemInterface $workflowFilesystem */
        $workflowFilesystem = $job->getContext()->get('filesystem');

        $destinationDefinition = $this->definitionManager->findOneBy(array('id' => $definitionId));
        $destinationFilesystem = $this->filesystemFactory->create($destinationDefinition);

        $exportPath = sha1(uniqid(mt_rand(), true));
        $publicUrl = $destinationDefinition->getUrl() . '/' . $exportPath;

        $logger->info('Distributing artifacts to ' . $publicUrl);

        $workflowFilesystem->copyToFilesystem('/', $destinationFilesystem, $exportPath);

        $job->getContext()->set('publicUrl', $publicUrl);

        if($parameters->getWorkspaceLifetime() != null)
        {
            $workspaceLifetime = $parameters->getWorkspaceLifetime();
            $logger->debug('Build schedule to remove destination files after ' . $workspaceLifetime . ' days');
            $schedule = $this->buildSchedule($workspaceLifetime);

            $fileConfiguration = new CleanupDirectoryConfiguration();
            $fileConfiguration->setPath($exportPath);
            $fileConfiguration->setFilesystemDefinition($destinationDefinition);

            $ticket = $this->jobManager->addJob('abc.workflow.task.cleanup_directory', $fileConfiguration, $schedule);

            $logger->debug('Added scheduled job {ticket} to remove directory', array('ticket' => $ticket));
        }
    }

    /**
     * @param int $workspaceLifetime
     * @return ScheduleInterface
     */
    private function buildSchedule($workspaceLifetime)
    {
        $date = new \DateTime();
        $date->add(new \DateInterval('P' . $workspaceLifetime . 'D'));
        $schedule = new Schedule();
        $schedule->setType('timestamp');
        $schedule->setExpression($date->getTimestamp());

        return $schedule;
    }
}