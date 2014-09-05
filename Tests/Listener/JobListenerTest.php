<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Listener;

use Abc\Bundle\JobBundle\Event\JobEvent;
use Abc\Bundle\JobBundle\Event\ReportEvent;
use Abc\Bundle\JobBundle\Job\JobInformation;
use Abc\Bundle\JobBundle\Job\Report\Report;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\WorkflowBundle\Entity\Execution;
use Abc\Bundle\WorkflowBundle\Listener\JobListener;
use Abc\Bundle\JobBundle\Job\Context\Context;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Configuration;
use Abc\Filesystem\Filesystem;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class JobListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystem;
    /** @var JobEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $jobEvent;
    /** @var JobInformation|\PHPUnit_Framework_MockObject_MockObject */
    protected $rootJob;
    /** @var Context */
    protected $context;
    /** @var JobListener */
    protected $subject;
    /** @var ExecutionManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $executionManager;

    public function setUp()
    {
        $this->filesystem       = $this->getMockBuilder('Abc\Filesystem\Filesystem')->disableOriginalConstructor()->getMock();
        $this->executionManager = $this->getMock('Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface');
        $this->context          = new Context();
        $this->jobEvent         = $this->createJoEventMock();
        $this->rootJob          = $this->createJobMock();

        $this->jobEvent->expects($this->any())
            ->method('getRootJob')
            ->willReturn($this->rootJob);

        $this->jobEvent->expects($this->any())
            ->method('getContext')
            ->willReturn($this->context);

        $this->subject = new JobListener($this->filesystem, $this->executionManager);
    }

    /**
     * @param Configuration $configuration
     * @dataProvider getConfiguration
     */
    public function testOnPrepareSetsFilesystem(Configuration $configuration)
    {
        $ticket     = 'foobar';
        $rootTicket = 'root-ticket';

        $this->jobEvent->expects($this->any())
            ->method('getTicket')
            ->willReturn($ticket);

        $this->rootJob->expects($this->any())
            ->method('getType')
            ->willReturn('workflow');

        $this->rootJob->expects($this->any())
            ->method('getTicket')
            ->willReturn($rootTicket);

        $this->rootJob->expects($this->once())
            ->method('getParameters')
            ->willReturn($configuration);

        $workflowFilesystem = $this->getMockBuilder('Abc\Filesystem\Filesystem')->disableOriginalConstructor()->getMock();

        if($configuration->getCreateDirectory())
        {
            $this->filesystem->expects($this->once())
                ->method('createFilesystem')
                ->with($rootTicket)
                ->willReturn($workflowFilesystem);
        }
        else
        {
            $this->filesystem->expects($this->never())
                ->method('createFilesystem');
        }

        $this->subject->onPrepare($this->jobEvent);

        if($configuration->getCreateDirectory())
        {
            $this->assertSame($workflowFilesystem, $this->jobEvent->getContext()->get('filesystem'));
        }
        else
        {
            $this->assertFalse($this->jobEvent->getContext()->has('filesystem'));
        }
    }

    public function testOnPrepareCatchesExceptions()
    {
        $ticket     = 'foobar';
        $rootTicket = 'root-ticket';

        $this->jobEvent->expects($this->any())
            ->method('getTicket')
            ->willReturn($ticket);

        $this->rootJob->expects($this->any())
            ->method('getType')
            ->willReturn('workflow');

        $this->rootJob->expects($this->any())
            ->method('getTicket')
            ->willReturn($rootTicket);

        $this->rootJob->expects($this->once())
            ->method('getParameters')
            ->willReturn(new Configuration('id', null, true, true));

        $this->filesystem->expects($this->once())
            ->method('createFilesystem')
            ->willThrowException(new \Exception);

        $this->subject->onPrepare($this->jobEvent);

    }

    /**
     * @param $parameters
     * @dataProvider getInvalidJobParameters
     */
    public function testOnPrepareWithInvalidParameters($parameters)
    {
        $ticket     = 'foobar';
        $rootTicket = 'root-ticket';

        $this->jobEvent->expects($this->any())
            ->method('getTicket')
            ->willReturn($ticket);

        $this->rootJob->expects($this->any())
            ->method('getType')
            ->willReturn('workflow');

        $this->rootJob->expects($this->any())
            ->method('getTicket')
            ->willReturn($rootTicket);

        $this->rootJob->expects($this->once())
            ->method('getParameters')
            ->willReturn($parameters);

        $this->filesystem->expects($this->never())
            ->method('createFilesystem');

        $this->subject->onPrepare($this->jobEvent);
    }

    /**
     * @param Configuration $configuration
     * @dataProvider getConfiguration
     */
    public function testOnPrepareSkipsIfsNoWorkflow(Configuration $configuration)
    {
        $ticket = 'foobar';

        $this->jobEvent->expects($this->any())
            ->method('getTicket')
            ->willReturn($ticket);

        $this->rootJob->expects($this->once())
            ->method('getType')
            ->willReturn('foobar');

        $this->rootJob->expects($this->never())
            ->method('getParameters');

        $this->subject->onPrepare($this->jobEvent);

        $this->assertFalse($this->context->has('filesystem'));
    }

    /**
     * @param Configuration $configuration
     * @dataProvider getConfiguration
     */
    public function testOnTerminatedRemovesFilesystem(Configuration $configuration)
    {
        $report = $this->getReportExpectations();

        $event = new ReportEvent($report);

        $ticket = 'ticket';

        $report->expects($this->any())
            ->method('getTicket')
            ->willReturn($ticket);

        $report->expects($this->any())
            ->method('getType')
            ->willReturn('workflow');

        $report->expects($this->any())
            ->method('getParameters')
            ->willReturn($configuration);

        if($configuration->getRemoveDirectory())
        {
            $this->filesystem->expects($this->once())
                ->method('exists')
                ->with($ticket)
                ->willReturn(true);

            $this->filesystem->expects($this->once())
                ->method('remove')
                ->with($ticket);
        }
        else
        {
            $this->filesystem->expects($this->never())
                ->method('remove');
        }

        $execution = $this->getMock('Abc\Bundle\WorkflowBundle\Model\ExecutionInterface');

        $execution->expects($this->once())
            ->method('setExecutionTime')
            ->with(123);
        $execution->expects($this->once())
            ->method('setStatus')
            ->with(Status::PROCESSED());

        $this->executionManager->expects($this->once())
            ->method('findOneBy')
            ->with(array('ticket' => $ticket))
            ->willReturn($execution);

        $this->executionManager->expects($this->once())
            ->method('update')
            ->with($execution);

        $this->subject->onTerminated($event);
    }

    /**
     * @param $parameters
     * @dataProvider getInvalidJobParameters
     */
    public function testOnTerminatedWithInvalidParameters($parameters = null)
    {
        $report = $this->getReportExpectations();
        $event  = new ReportEvent($report);

        $ticket = 'ticket';

        $report->expects($this->any())
            ->method('getTicket')
            ->willReturn($ticket);

        $report->expects($this->any())
            ->method('getType')
            ->willReturn('workflow');

        $report->expects($this->any())
            ->method('getParameters')
            ->willReturn($parameters);

        $this->subject->onTerminated($event);

        $this->filesystem->expects($this->never())
            ->method('remove');
    }

    /**
     * @param Configuration $configuration
     * @dataProvider getConfiguration
     */
    public function testOnTerminatedSkipsIfNoWorkflow(Configuration $configuration)
    {
        $report = $this->getReportExpectations();
        $event  = new ReportEvent($report);

        $ticket = 'ticket';

        $report->expects($this->any())
            ->method('getType')
            ->willReturn('foobar');

        $report->expects($this->never())
            ->method('getParameters');

        $this->subject->onTerminated($event);
    }

    public function testOnTerminatedCatchesFilesystemExceptions()
    {
        $report = $this->getReportExpectations();
        $event  = new ReportEvent($report);

        $report->expects($this->any())
            ->method('getTicket')
            ->willReturn('ticket');

        $report->expects($this->any())
            ->method('getType')
            ->willReturn('workflow');

        $report->expects($this->any())
            ->method('getParameters')
            ->willReturn(new Configuration('id', null, true, true));

        $this->filesystem->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $this->filesystem->expects($this->once())
            ->method('remove')
            ->willThrowException(new \Exception);

        $this->subject->onTerminated($event);
    }

    public function getInvalidJobParameters()
    {
        return array(
            array(null),
            array('foo'),
            array($this->getMock('\Serializable'))
        );
    }

    public static function getConfiguration()
    {
        return array(
            array(new Configuration('id', null, true, true)),
            array(new Configuration('id', null, false, false))
        );
    }

    /**
     * @return JobInformation|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createJobMock()
    {
        return $this->getMock('Abc\Bundle\JobBundle\Job\Job');
    }

    /**
     * @return JobEvent|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createJoEventMock()
    {
        return $this->getMockBuilder('Abc\Bundle\JobBundle\Event\JobEvent')->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getReportExpectations()
    {
        $report = $this->getMockBuilder('Abc\Bundle\JobBundle\Job\Report\Report')->disableOriginalConstructor()->getMock();
        $report->expects($this->any())
            ->method('getExecutionTime')
            ->willReturn(123);
        $report->expects($this->any())
            ->method('getStatus')
            ->willReturn(Status::PROCESSED());

        return $report;
    }
}