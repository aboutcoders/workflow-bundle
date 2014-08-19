<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Listener;

use Abc\Bundle\JobBundle\Event\ReportEvent;
use Abc\Bundle\JobBundle\Job\Report\Report;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\WorkflowBundle\Entity\Execution;
use Abc\Bundle\WorkflowBundle\Listener\JobListener;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\Context\Context;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\Workflow;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Abc\Filesystem\Filesystem;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class JobListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystem;
    /** @var Job|\PHPUnit_Framework_MockObject_MockObject */
    protected $job;
    /** @var Job|\PHPUnit_Framework_MockObject_MockObject */
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
        $this->job              = $this->createJobMock();
        $this->rootJob          = $this->createJobMock();

        $this->job->expects($this->any())
            ->method('getRootJob')
            ->will($this->returnValue($this->rootJob));

        $this->job->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($this->context));

        $this->subject = new JobListener($this->filesystem, $this->executionManager);
    }

    /**
     * @param WorkflowInterface $workflow
     * @dataProvider getWorkflow
     */
    public function testOnPrepareSetsFilesystem(WorkflowInterface $workflow)
    {
        $ticket     = 'foobar';
        $rootTicket = 'root-ticket';

        $this->job->expects($this->any())
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
            ->willReturn($workflow);

        $workflowFilesystem = $this->getMockBuilder('Abc\Filesystem\Filesystem')->disableOriginalConstructor()->getMock();

        if ($workflow->getCreateDirectory()) {
            $this->filesystem->expects($this->once())
                ->method('createFilesystem')
                ->with($rootTicket)
                ->willReturn($workflowFilesystem);
        } else {
            $this->filesystem->expects($this->never())
                ->method('createFilesystem');
        }

        $this->subject->onPrepare($this->job);

        if ($workflow->getCreateDirectory()) {
            $this->assertSame($workflowFilesystem, $this->job->getContext()->get('filesystem'));
        } else {
            $this->assertFalse($this->job->getContext()->has('filesystem'));
        }
    }

    public function testOnPrepareCatchesExceptions()
    {
        $ticket     = 'foobar';
        $rootTicket = 'root-ticket';

        $this->job->expects($this->any())
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
            ->willReturn(self::createWorkflow(true, true));

        $this->filesystem->expects($this->once())
            ->method('createFilesystem')
            ->willThrowException(new \Exception);

        $this->subject->onPrepare($this->job);

    }

    /**
     * @param $parameters
     * @dataProvider getInvalidJobParameters
     */
    public function testOnPrepareWithInvalidParameters($parameters)
    {
        $ticket     = 'foobar';
        $rootTicket = 'root-ticket';

        $this->job->expects($this->any())
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

        $this->subject->onPrepare($this->job);
    }

    /**
     * @param WorkflowInterface $workflow
     * @dataProvider getWorkflow
     */
    public function testOnPrepareSkipsIfsNoWorkflow(WorkflowInterface $workflow)
    {
        $ticket = 'foobar';

        $this->job->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($ticket));

        $this->rootJob->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('foobar'));

        $this->rootJob->expects($this->never())
            ->method('getParameters');

        $this->subject->onPrepare($this->job);

        $this->assertFalse($this->context->has('filesystem'));
    }

    /**
     * @param WorkflowInterface $workflow
     * @dataProvider getWorkflow
     */
    public function testOnTerminateRemovesFilesystem(WorkflowInterface $workflow)
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
            ->willReturn($workflow);

        if ($workflow->getRemoveDirectory()) {
            $this->filesystem->expects($this->once())
                ->method('remove')
                ->with($ticket);
        } else {
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

        $this->subject->onTerminate($event);
    }

    /**
     * @param $parameters
     * @dataProvider getInvalidJobParameters
     */
    public function testOnTerminateWithInvalidParameters($parameters = null)
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

        $this->subject->onTerminate($event);

        $this->filesystem->expects($this->never())
            ->method('remove');
    }

    /**
     * @param WorkflowInterface $workflow
     * @dataProvider getWorkflow
     */
    public function testOnTerminateSkipsIfNoWorkflow(WorkflowInterface $workflow)
    {
        $report = $this->getReportExpectations();
        $event  = new ReportEvent($report);

        $ticket = 'ticket';

        $report->expects($this->any())
            ->method('getType')
            ->willReturn('foobar');

        $report->expects($this->never())
            ->method('getParameters');

        $this->subject->onTerminate($event);
    }

    public function testOnTerminateCatchesFilesystemExceptions()
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
            ->willReturn($this->createWorkflow(true, true));

        $this->filesystem->expects($this->once())
            ->method('remove')
            ->willThrowException(new \Exception);

        $this->subject->onTerminate($event);
    }

    public function getInvalidJobParameters()
    {
        return array(
            array(null),
            array('foo'),
            array($this->getMock('\Serializable'))
        );
    }

    public static function getWorkflow()
    {
        return array(
            array(static::createWorkflow(true, true)),
            array(static::createWorkflow(false, false))
        );
    }

    public static function createWorkflow($createDirectory, $removeDirectory)
    {
        $workflow = new Workflow();
        $workflow->setName('testWorkflow');
        $workflow->setCreateDirectory($createDirectory);
        $workflow->setRemoveDirectory($removeDirectory);

        return $workflow;
    }

    /**
     * @return Job|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createJobMock()
    {
        return $this->getMock('Abc\Bundle\JobBundle\Job\Job');
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