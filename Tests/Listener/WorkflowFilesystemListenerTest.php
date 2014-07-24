<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Listener;

use Abc\Bundle\WorkflowBundle\Listener\WorkflowFilesystemListener;
use Abc\Bundle\JobBundle\Job\Context\Context;
use Abc\Bundle\JobBundle\Event\JobEvent;
use Abc\Filesystem\Filesystem;

class WorkflowFilesystemListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystem;
    /** @var JobEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $jobEvent;
    /** @var JobEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $rootJob;
    /** @var Context */
    protected $context;
    /** @var WorkflowFilesystemListener */
    protected $subject;

    public function setUp()
    {
        $this->filesystem = $this->getMockBuilder('Abc\Filesystem\Filesystem')->disableOriginalConstructor()->getMock();
        $this->context    = new Context();
        $this->jobEvent   = $this->createJobEventMock();
        $this->rootJob    = $this->createJobEventMock();

        $this->jobEvent->expects($this->any())
            ->method('getRootJob')
            ->will($this->returnValue($this->rootJob));

        $this->jobEvent->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($this->context));

        $this->subject = new WorkflowFilesystemListener($this->filesystem);
    }

    public function testOnPrepareAddsFilesystemToContext()
    {
        $ticket = 'foobar';

        $this->jobEvent->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($ticket));

        $this->rootJob->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('workflow'));

        $workflowFilesystem = $this->getMockBuilder('Abc\Filesystem\Filesystem')->disableOriginalConstructor()->getMock();

        $this->filesystem->expects($this->once())
            ->method('createFilesystem')
            ->with($ticket)
            ->will($this->returnValue($workflowFilesystem));

        $this->subject->onJobPrepare($this->jobEvent);

        $this->assertSame($workflowFilesystem, $this->jobEvent->getContext()->get('filesystem'));
    }

    /**
     * @todo remove created directory after test execution
     */
    public function testOnPrepareDoesNothingIfJobIsNoWorkflow()
    {
        $ticket = 'foobar';

        $this->jobEvent->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($ticket));

        $this->rootJob->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('foobar'));

        $this->filesystem->expects($this->never())
            ->method('createFilesystem');

        $this->subject->onJobPrepare($this->jobEvent);

        $this->assertFalse($this->context->has('filesystem'));
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createJobEventMock()
    {
        return $this->getMockBuilder('Abc\Bundle\JobBundle\Event\JobEvent')->disableOriginalConstructor()->getMock();
    }
}