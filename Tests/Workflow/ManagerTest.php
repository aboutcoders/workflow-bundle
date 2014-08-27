<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Workflow;

use Abc\Bundle\JobBundle\Job\ManagerInterface as JobManager;
use Abc\Bundle\JobBundle\Job\Report\ReportInterface;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\WorkflowBundle\Model\Execution;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\Workflow;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Exception\WorkflowNotFoundException;
use Abc\Bundle\WorkflowBundle\Workflow\Manager;
use Abc\Bundle\WorkflowBundle\Workflow\Configuration;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{

    /** @var JobManager|\PHPUnit_Framework_MockObject_MockObject */
    private $jobManager;
    /** @var ExecutionManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $executionManager;
    /** @var TaskManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $taskManager;
    /** @var WorkflowManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $workflowManager;


    /** @var Manager */
    private $subject;

    public function setUp()
    {
        $this->jobManager       = $this->getMock('Abc\Bundle\JobBundle\Job\ManagerInterface');
        $this->executionManager = $this->getMock('Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface');
        $this->taskManager      = $this->getMock('Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface');
        $this->workflowManager  = $this->getMock('Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface');

        $this->subject = new Manager($this->jobManager, $this->workflowManager, $this->taskManager);
        $this->subject->setExecutionManager($this->executionManager);
    }

    public function testCancel()
    {
        $ticket = 'ticket';

        $this->jobManager->expects($this->once())
            ->method('cancelJob')
            ->with($ticket);

        $this->subject->cancel($ticket);
    }

    /**
     * @param      $id
     * @param null $parameters
     * @param null $schedule
     * @param null $response
     * @dataProvider getExecuteData
     */
    public function testExecute($id, $parameters = null, $schedule = null, $response = null)
    {
        $workflow = new Workflow();
        $workflow->setId($id);
        $workflow->setCreateDirectory(false);
        $workflow->setRemoveDirectory(false);

        $execution = new Execution();

        $this->workflowManager->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($workflow);

        $self = $this;
        $this->jobManager->expects($this->once())
            ->method('addJob')
            ->willReturnCallback(
                function ($arg1, $arg2 = null, $arg3 = null, $arg4 = null) use ($self, $parameters, $schedule, $response)
                {
                    /** @var Configuration $arg2 */
                    $self->assertEquals('workflow', $arg1);
                    $self->assertInstanceOf('Abc\Bundle\WorkflowBundle\Workflow\Configuration', $arg2);
                    $self->assertSame($arg2->getParameters(), $parameters);
                    $self->assertEquals($schedule, $arg3);

                    if($response == null)
                    {
                        $self->assertInstanceOf('Abc\Bundle\WorkflowBundle\Workflow\Response', $arg4);
                    }
                    else
                    {
                        $self->assertEquals($response, $arg4);
                    }

                    return 'ticket';
                }
            );

        $this->executionManager->expects($this->once())
            ->method('create')
            ->with('ticket', $workflow)
            ->willReturn($execution);

        $returnValue = $this->subject->execute($id, $parameters, $schedule, $response);

        $this->assertEquals($execution, $returnValue);
    }

    /**
     * @expectedException \Abc\Bundle\WorkflowBundle\Workflow\Exception\WorkflowNotFoundException
     */
    public function testExecuteThrowsWorkflowNotFoundException()
    {
        $returnValue = $this->subject->execute(10);
    }

    /**
     * @param Status $status
     * @dataProvider getTerminatedStatusValues
     */
    public function testGetProgressWithTerminatedJob(Status $status)
    {
        $ticket = 'ticket';
        $report = $this->getMock('Abc\Bundle\JobBundle\Job\Report\ReportInterface');

        $report->expects($this->any())
            ->method('getStatus')
            ->willReturn($status);

        $this->jobManager->expects($this->once())
            ->method('getReport')
            ->with($ticket)
            ->willReturn($report);

        $this->assertEquals(100, $this->subject->getProgress($ticket));
    }

    /**
     * @param       $status
     * @param array $tasks
     * @param       $expectedProgress
     * @dataProvider getProgressData
     */
    public function testGetProgressWithProcessingJob($status, $index, array $tasks, $expectedProgress)
    {
        $ticket = 'ticket';
        $report = $this->getMock('Abc\Bundle\JobBundle\Job\Report\ReportInterface');
        $configuration = new Configuration(1);
        $configuration->setIndex($index);

        $report->expects($this->any())
            ->method('getStatus')
            ->willReturn($status);

        $report->expects($this->any())
            ->method('getParameters')
            ->willReturn($configuration);

        $this->jobManager->expects($this->once())
            ->method('getReport')
            ->with($ticket)
            ->willReturn($report);

        $this->taskManager->expects($this->once())
            ->method('findWorkflowTasks')
            ->with($configuration->getId())
            ->willReturn($tasks);

        $this->assertEquals($expectedProgress, $this->subject->getProgress($ticket));
    }

    public function testGetReport()
    {
        $ticket = 'ticket';
        $report = $this->getMock('Abc\Bundle\JobBundle\Job\Report\ReportInterface');

        $this->jobManager->expects($this->once())
            ->method('getReport')
            ->with($ticket)
            ->willReturn($report);

        $this->assertSame($report, $this->subject->getReport($ticket));
    }

    public function testGetStatus()
    {
        $ticket = 'ticket';
        $status = Status::CANCELLED();

        $this->jobManager->expects($this->once())
            ->method('getStatus')
            ->with($ticket)
            ->willReturn($status);

        $this->assertSame($status, $this->subject->getStatus($ticket));
    }

    public function getExecuteData()
    {
        return array(
            array(10),
            array(10, $this->getMock('\Serializable')),
            array(10, $this->getMock('\Serializable'), $this->getMock('Abc\Bundle\SchedulerBundle\Model\ScheduleInterface')),
            array(
                10,
                $this->getMock('\Serializable'),
                $this->getMock('Abc\Bundle\SchedulerBundle\Model\ScheduleInterface'),
                $this->getMock('Abc\Bundle\WorkflowBundle\Workflow\Response')
            )
        );
    }

    public static function getProgressData()
    {
        return array(
            array(Status::REQUESTED(), 0, array(1, 2, 3, 4), 0),
            array(Status::PROCESSING(), 1, array(1, 2, 3, 4), 25),
            array(Status::PROCESSING(), 2, array(1, 2, 3, 4), 50),
            array(Status::PROCESSING(), 3, array(1, 2, 3, 4), 75),
            array(Status::PROCESSING(), 4, array(1, 2, 3, 4), 100)
        );
    }

    /**
     * @return array
     */
    public function getTerminatedStatusValues()
    {
        return array(
            array(Status::PROCESSED()),
            array(Status::CANCELLED()),
            array(Status::ERROR()),
        );
    }
}