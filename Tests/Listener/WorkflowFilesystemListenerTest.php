<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Listener;

use Abc\Bundle\WorkflowBundle\Listener\WorkflowFilesystemListener;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\Context\Context;
use Abc\Filesystem\Filesystem;

class WorkflowFilesystemListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystem;
    /** @var Job|\PHPUnit_Framework_MockObject_MockObject */
    protected $job;
    /** @var Job|\PHPUnit_Framework_MockObject_MockObject */
    protected $rootJob;
    /** @var Context */
    protected $context;
    /** @var WorkflowFilesystemListener */
    protected $subject;

    public function setUp()
    {
        $this->filesystem = $this->getMockBuilder('Abc\Filesystem\Filesystem')->disableOriginalConstructor()->getMock();
        $this->context    = new Context();
        $this->job        = $this->createJobMock();
        $this->rootJob    = $this->createJobMock();

        $this->job->expects($this->any())
            ->method('getRootJob')
            ->will($this->returnValue($this->rootJob));

        $this->job->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($this->context));

        $this->subject = new WorkflowFilesystemListener($this->filesystem);
    }

    public function testOnPrepareAddsFilesystemToContext()
    {
        $ticket = 'foobar';
        $rootTicket = 'root-ticket';

        $this->job->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($ticket));

        $this->rootJob->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('workflow'));

        $this->rootJob->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($rootTicket));

        $workflowFilesystem = $this->getMockBuilder('Abc\Filesystem\Filesystem')->disableOriginalConstructor()->getMock();

        $this->filesystem->expects($this->once())
            ->method('createFilesystem')
            ->with($rootTicket)
            ->will($this->returnValue($workflowFilesystem));

        $this->subject->onJobPrepare($this->job);

        $this->assertSame($workflowFilesystem, $this->job->getContext()->get('filesystem'));
    }

    /**
     * @todo remove created directory after test execution
     */
    public function testOnPrepareDoesNothingIfJobIsNoWorkflow()
    {
        $ticket = 'foobar';

        $this->job->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($ticket));

        $this->rootJob->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('foobar'));

        $this->filesystem->expects($this->never())
            ->method('createFilesystem');

        $this->subject->onJobPrepare($this->job);

        $this->assertFalse($this->context->has('filesystem'));
    }

    /**
     * @return Job|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createJobMock()
    {
        return $this->getMock('Abc\Bundle\JobBundle\Job\Job');
    }
}