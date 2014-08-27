<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Executable;

use Abc\Bundle\FileDistributionBundle\Model\DefinitionManagerInterface;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\ManagerInterface;
use Abc\Bundle\JobBundle\Model\JobManagerInterface;
use Abc\Bundle\WorkflowBundle\Executable\ArtifactDistributor;
use Abc\Bundle\WorkflowBundle\Executable\DistributeArtifactsParameter;
use Abc\Bundle\JobBundle\Job\Context\Context;
use Abc\Bundle\WorkflowBundle\Workflow\CleanupDirectoryConfiguration;
use Abc\Filesystem\FilesystemFactoryInterface;
use Psr\Log\NullLogger;

class ArtifactDistributorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|Job */
    protected $job;
    /** @var Context */
    protected $context;
    /** @var ArtifactDistributor */
    protected $subject;
    /** @var DistributeArtifactsParameter */
    protected $jobParameters;
    /** @var \PHPUnit_Framework_MockObject_MockObject|DefinitionManagerInterface */
    protected $definitionManager;
    /** @var \PHPUnit_Framework_MockObject_MockObject|FilesystemFactoryInterface */
    protected $filesystemFactory;
    /** @var  \PHPUnit_Framework_MockObject_MockObject|\Abc\Filesystem\Filesystem */
    protected $destinationFilesystem;
    /** @var \PHPUnit_Framework_MockObject_MockObject|ManagerInterface */
    protected $jobManager;
    /** @var string */
    protected $destinationDirectory;

    public function setUp()
    {
        $this->definitionManager     = $this->getMock('Abc\Bundle\FileDistributionBundle\Model\DefinitionManagerInterface');
        $this->filesystemFactory     = $this->getMock('Abc\Filesystem\FilesystemFactoryInterface');
        $this->jobManager            = $this->getMock('Abc\Bundle\JobBundle\Job\ManagerInterface');
        $this->destinationFilesystem = $this->getMockBuilder('Abc\Filesystem\Filesystem')->disableOriginalConstructor()->getMock();
        $this->destinationDirectory  = '/';
        $this->getDestinationFilesystemExpectations();

        $this->subject = new ArtifactDistributor($this->definitionManager, $this->filesystemFactory, $this->jobManager);
    }

    public function testExecuteWithFilesDistributesFiles()
    {
        $this->jobManager->expects($this->once())
            ->method('addJob')
            ->with('abc.workflow.task.cleanup_directory');
        $this->getJobExpectations();
        $this->subject->execute($this->job);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteWithEmptyFilesystemDefinitionThrowsException()
    {
        $this->getJobExpectations(true);
        $this->subject->execute($this->job);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getWorkflowFilesystemExpectations()
    {
        $filesystem = $this->getMockBuilder('Abc\Filesystem\Filesystem')->disableOriginalConstructor()->getMock();
        $filesystem->expects($this->any())
            ->method('copyToFilesystem')
            ->with('/', $this->destinationFilesystem);

        return $filesystem;
    }

    private function getDestinationFilesystemExpectations()
    {
        $definition = $this->getMock('Abc\Bundle\FileDistributionBundle\Model\DefinitionInterface');

        $definition
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('http://domain.tld'));

        $this->definitionManager
            ->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($definition));

        $this->filesystemFactory
            ->expects($this->any())
            ->method('create')
            ->with($definition)
            ->will($this->returnValue($this->destinationFilesystem));
    }

    private function getJobExpectations($emptyParameters = false)
    {
        $workflowFilesystem = $this->getWorkflowFilesystemExpectations();

        $this->context = new Context();
        $this->context->set('logger', new NullLogger());
        $this->context->set('filesystem', $workflowFilesystem);
        $this->jobParameters = new DistributeArtifactsParameter();
        if (!$emptyParameters) {
            $this->jobParameters->setDefinitionId(1);
        }

        $this->job = $this->getMock('Abc\Bundle\JobBundle\Job\Job');
        $this->job->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($this->context));
        $this->job->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue($this->jobParameters));
    }
}
 