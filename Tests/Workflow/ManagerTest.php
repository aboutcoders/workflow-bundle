<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Workflow;

use Abc\Bundle\JobBundle\Job\ManagerInterface as JobManager;
use Abc\Bundle\WorkflowBundle\Model\Execution;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
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

    /** @var WorkflowManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $workflowManager;
    /** @var ExecutionManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $executionManager;
    /** @var JobManager|\PHPUnit_Framework_MockObject_MockObject */
    private $jobManager;

    /** @var Manager */
    private $subject;

    public function setUp()
    {
        $this->jobManager       = $this->getMock('Abc\Bundle\JobBundle\Job\ManagerInterface');
        $this->workflowManager  = $this->getMock('Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface');
        $this->executionManager = $this->getMock('Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface');

        $this->subject = new Manager($this->jobManager, $this->workflowManager, $this->executionManager);
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

    public function getExecuteData()
    {
        return array(
            array(10),
            array(10, $this->getMock('\Serializable')),
            array(10, $this->getMock('\Serializable'), $this->getMock('Abc\Bundle\SchedulerBundle\Model\ScheduleInterface')),
            array(10, $this->getMock('\Serializable'), $this->getMock('Abc\Bundle\SchedulerBundle\Model\ScheduleInterface'), $this->getMock('Abc\Bundle\WorkflowBundle\Workflow\Response'))
        );
    }
}