<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Executable;

use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\Context\Context;

use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Executable\WorkflowExecutable;
use Abc\Bundle\WorkflowBundle\Model\Schedule;
use Abc\Bundle\WorkflowBundle\Model\Execution;
use Abc\Bundle\WorkflowBundle\Model\Task;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskType;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Psr\Log\NullLogger;

class WorkflowExecutableTest extends \PHPUnit_Framework_TestCase
{

    /** @var TaskManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $taskManager;
    /** @var ExecutionManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $executionManager;
    /** @var WorkflowManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $workflowManager;

    /** @var WorkflowExecutable */
    protected $subject;

    public function setUp()
    {
        $this->workflowManager  = $this->getMock('Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface');
        $this->taskManager      = $this->getMock('Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface');
        $this->executionManager = $this->getMock('Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface');

        $this->subject = new WorkflowExecutable($this->workflowManager, $this->taskManager, $this->executionManager);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteThrowsExceptionIfParameterIsInvalid()
    {
        $job = $this->createJob('ticket', 'workflow');

        $this->subject->execute($job);
    }


    public function testExecuteWithRootJobCreatesExecutionAndAddsFirstTask()
    {
        $ticket             = 'ticket';
        $workflowId         = 'workflow-id';
        $workflowParameters = $this->getMock('\Serializable');

        $workflow = new Workflow();
        $workflow->setId($workflowId);
        $workflow->setParameters($workflowParameters);

        $task = new Task();
        $task->setType(new TaskType());
        $task->getType()->setJobType('foobar');
        $task->getParameters(clone $workflowParameters);
        $task->setSchedule(new Schedule());

        $job = $this->createJob($ticket, 'workflow', $workflow);

        $execution = new Execution();

        $self = $this;

        $job->expects($this->any())
            ->method('isCallback')
            ->will($this->returnValue(false));

        $this->executionManager->expects($this->once())
            ->method('create')
            ->will($this->returnValue($execution));

        $this->executionManager->expects($this->once())
            ->method('update')
            ->will(
                $this->returnCallback(
                    function ($execution) use ($self, $workflow, $ticket)
                    {
                        /** @var Execution $execution */
                        $self->assertEquals($workflow, $execution->getWorkflow($workflow));
                        $self->assertEquals($ticket, $execution->getTicket());

                        return null;
                    }
                )
            );

        $this->workflowManager->expects($this->once())
            ->method('findById')
            ->with($workflow->getId())
            ->will($this->returnValue($workflow));

        $this->taskManager->expects($this->once())
            ->method('findNextWorkflowTask')
            ->with($workflowId, 0)
            ->will($this->returnValue($task));

        $job->expects($this->once())
            ->method('addChildJob')
            ->with($task->getType()->getJobType(), $task->getParameters(), $task->getSchedule());

        $job->expects($this->once())
            ->method('update');

        $this->subject->execute($job);

        $this->assertEquals(0, $workflow->getIndex());
        $this->assertTrue($job->getContext()->has('parameters'));
        $this->assertSame($workflow->getParameters(), $job->getContext()->get('parameters'));
    }

    public function testExecuteWithChildAddsNextChildJob()
    {
        $ticket             = 'ticket';
        $workflowId         = 'workflow-id';
        $workflowParameters = $this->getMock('\Serializable');

        $workflow = new Workflow();
        $workflow->setId($workflowId);
        $workflow->setParameters($workflowParameters);
        $workflow->setIndex(4);

        $task = new Task();
        $task->setType(new TaskType());
        $task->getType()->setJobType('foobar');
        $task->getParameters(clone $workflowParameters);

        $job      = $this->createJob($ticket, 'workflow', $workflow);
        $childJob = $this->createJob('task-ticket', 'foobar');

        $self = $this;

        $job->expects($this->any())
            ->method('IsTriggeredByCallback')
            ->will($this->returnValue(true));

        $job->expects($this->any())
            ->method('getCallerJob')
            ->will($this->returnValue($childJob));

        $this->taskManager->expects($this->once())
            ->method('findNextWorkflowTask')
            ->with($workflowId, $workflow->getIndex() + 1)
            ->will($this->returnValue($task));

        $job->expects($this->once())
            ->method('addChildJob')
            ->with($task->getType()->getJobType(), $task->getParameters());

        $job->expects($this->once())
            ->method('update');

        $this->subject->execute($job);

        $this->assertEquals(5, $workflow->getIndex());
    }

    public function testExecuteWithChildJobDoesNotCreateExecution()
    {
        $ticket             = 'workflow-ticket';
        $workflowId         = 'workflow-id';
        $workflowParameters = $this->getMock('\Serializable');

        $workflow = new Workflow();
        $workflow->setId($workflowId);
        $workflow->setParameters($workflowParameters);

        $job      = $this->createJob($ticket, 'workflow', $workflow);
        $childJob = $this->createJob('task-ticket', 'foobar');

        $job->expects($this->any())
            ->method('isTriggeredByCallback')
            ->will($this->returnValue(true));

        $job->expects($this->any())
            ->method('getCallerJob')
            ->will($this->returnValue($childJob));

        $this->executionManager->expects($this->never())
            ->method('update');

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