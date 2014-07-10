<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Listener;

use Abc\Bundle\WorkflowBundle\Listener\WorkflowFilesystemListener;
use Abc\File\DistributionManagerInterface;
use Abc\File\FilesystemInterface;
use Abc\Bundle\JobBundle\Event\JobEvent;
use Abc\Bundle\JobBundle\Api\Context;

class WorkflowFilesystemListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilesystemInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $baseFilesystem;
    /** @var DistributionManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $manager;
    /** @var JobEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $jobEvent;
    /** @var Context */
    protected $context;
    /** @var WorkflowFilesystemListener */
    protected $subject;

    public function setUp()
    {
        $this->baseFilesystem = $this->getMock('Abc\File\FilesystemInterface');
        $this->manager = $this->getMock('Abc\File\DistributionManagerInterface');
        $this->context = new Context();
        $this->jobEvent = $this->getMockBuilder('Abc\Bundle\JobBundle\Event\JobEvent')->disableOriginalConstructor()->getMock();

        $this->jobEvent->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($this->context));

        $this->subject = new WorkflowFilesystemListener($this->manager, $this->baseFilesystem);
    }

    public function testOnPrepareAddsFilesystemToContext()
    {
        $ticket = 'foobar';

        $workflowFilesystem = $this->getMock('Abc\File\FilesystemInterface');

        $this->jobEvent->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($ticket));

        $this->jobEvent->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('workflow'));

        $this->manager->expects($this->once())
            ->method('createFilesystem')
            ->with($this->baseFilesystem, $ticket)
            ->will($this->returnValue($workflowFilesystem));

        $this->subject->onJobPrepare($this->jobEvent);

        $this->assertSame($workflowFilesystem, $this->jobEvent->getContext()->get('filesystem'));
    }

    public function testOnPrepareDoesNothingIfJobIsNoWorkflow()
    {
        $ticket = 'foobar';

        $workflowFilesystem = $this->getMock('Abc\File\FilesystemInterface');

        $this->jobEvent->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($ticket));

        $this->jobEvent->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('foobar'));

        $this->manager->expects($this->never())
            ->method('createFilesystem');

        $this->subject->onJobPrepare($this->jobEvent);

        $this->assertFalse($this->context->has('filesystem'));
    }
}