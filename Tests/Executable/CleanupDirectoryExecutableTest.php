<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Executable;


use Abc\Bundle\JobBundle\Job\Context\Context;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\WorkflowBundle\Executable\CleanupDirectoryExecutable;
use Abc\Bundle\WorkflowBundle\Workflow\CleanupDirectoryConfiguration;
use Abc\Filesystem\FilesystemFactoryInterface;
use Psr\Log\NullLogger;

class CleanupDirectoryExecutableTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilesystemFactoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystemFactory;

    /** @var CleanupDirectoryExecutable */
    protected $subject;

    /**
     * @before
     */
    public function setupSubject()
    {
        $this->filesystemFactory = $this->getMock('Abc\Filesystem\FilesystemFactoryInterface');

        $this->subject = new CleanupDirectoryExecutable($this->filesystemFactory);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Parameters must be an instance of Abc\Bundle\WorkflowBundle\Workflow\CleanupDirectoryConfiguration
     */
    public function testExecuteWithInValidParametersThrowsException()
    {
        $job = $this->createJob('ticket', 'abc.target_platform.task.cleanup_directory');
        $this->subject->execute($job);

    }

    public function testExecuteWithValidParametersRemovesDirectory()
    {
        $filesystem = $this->getMock('Abc\Filesystem\FilesystemInterface');
        $definition = $this->getMock('Abc\Filesystem\DefinitionInterface');
        $parameter  = new CleanupDirectoryConfiguration();
        $parameter->setPath('abc/def');
        $parameter->setFilesystemDefinition($definition);

        $this->filesystemFactory->expects($this->once())
            ->method('create')
            ->with($definition)
            ->willReturn($filesystem);

        $filesystem->expects($this->once())
            ->method('remove')
            ->with('/');

        $job = $this->createJob('ticket', 'abc.target_platform.task.cleanup_directory', $parameter);
        $job->expects($this->once())->method('removeSchedule');

        $this->subject->execute($job);

    }

    /**
     * @param mixed $ticket
     * @param mixed $type
     * @param mixed $parameter
     * @return Job|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createJob($ticket = null, $type = null, $parameter = null)
    {
        $job = $this->getMock('Abc\Bundle\JobBundle\Job\Job');

        $job->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($ticket));

        $job->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $job->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue($parameter));

        $context = new Context();
        $context->set('logger', new NullLogger());

        $job->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($context));

        return $job;
    }
}
 