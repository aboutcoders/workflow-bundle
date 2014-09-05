<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Executable;

use Abc\Bundle\JobBundle\Job\Exception\TerminateException;
use Abc\Bundle\JobBundle\Job\Job;
use Abc\Bundle\JobBundle\Job\Context\Context;
use Abc\Bundle\JobBundle\Job\Response\ErrorResponse;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\WorkflowBundle\Executable\WorkflowExecutor;
use Abc\Bundle\WorkflowBundle\Model\Schedule;
use Abc\Bundle\WorkflowBundle\Model\Task;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskType;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Configuration;
use Abc\Bundle\WorkflowBundle\Workflow\Response;
use Psr\Log\NullLogger;

class WorkflowExecutorTest extends \PHPUnit_Framework_TestCase
{

    /** @var TaskManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $taskManager;
    /** @var WorkflowManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $workflowManager;

    /** @var WorkflowExecutor */
    protected $subject;

    public function setUp()
    {
        $this->workflowManager = $this->getMock('Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface');
        $this->taskManager     = $this->getMock('Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface');

        $this->subject = new WorkflowExecutor($this->workflowManager, $this->taskManager);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteThrowsExceptionIfParameterIsInvalid()
    {
        $job = $this->createJob('ticket', 'workflow');

        $this->subject->execute($job);
    }


    public function testExecuteWithRootJobAddsFirstTask()
    {
        $ticket             = 'ticket';
        $workflowId         = 'workflow-id';
        $workflowParameters = $this->getMock('\Serializable');

        $configuration = new Configuration($workflowId, $workflowParameters);

        $task = new Task();
        $task->setType(new TaskType());
        $task->getType()->setJobType('foobar');
        $task->getType()->setName('first-task');
        $task->getParameters(clone $workflowParameters);
        $task->setSchedule(new Schedule());

        $job      = $this->createJob($ticket, 'workflow', $configuration);
        $response = new Response();

        $self = $this;

        $job->expects($this->any())
            ->method('isCallback')
            ->will($this->returnValue(false));

        $job->expects($this->any())
            ->method('getResponseBody')
            ->will($this->returnValue($response));

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

        $this->assertEquals(0, $configuration->getIndex());
        $this->assertTrue($job->getContext()->has('parameters'));
        $this->assertSame($configuration->getParameters(), $job->getContext()->get('parameters'));
        $this->assertEquals(array('FIRST-TASK'), $job->getResponseBody()->getActions());
    }

    public function testExecuteWithChildAddsNextChildJob()
    {
        $ticket             = 'ticket';
        $workflowId         = 'workflow-id';
        $workflowParameters = $this->getMock('\Serializable');

        $configuration = new Configuration($workflowId, $workflowParameters);
        $configuration->setIndex(4);

        $task = new Task();
        $task->setType(new TaskType());
        $task->getType()->setJobType('foobar');
        $task->getType()->setName('second-task');
        $task->getParameters(clone $workflowParameters);

        $job      = $this->createJob($ticket, 'workflow', $configuration);
        $childJob = $this->createJobInformation(Status::PROCESSED(), 'task-ticket', 'foobar');
        $response = new Response();

        $job->expects($this->any())
            ->method('IsTriggeredByCallback')
            ->will($this->returnValue(true));

        $job->expects($this->any())
            ->method('getResponseBody')
            ->will($this->returnValue($response));

        $job->expects($this->any())
            ->method('getCallerJob')
            ->will($this->returnValue($childJob));

        $this->taskManager->expects($this->once())
            ->method('findNextWorkflowTask')
            ->with($workflowId, $configuration->getIndex() + 1)
            ->will($this->returnValue($task));

        $job->expects($this->once())
            ->method('addChildJob')
            ->with($task->getType()->getJobType(), $task->getParameters());

        $job->expects($this->once())
            ->method('update');

        $this->subject->execute($job);

        $this->assertEquals(5, $configuration->getIndex());
        $this->assertEquals(array('SECOND-TASK'), $job->getResponseBody()->getActions());
    }


    public function testExecuteThrowsTerminateExceptionOnChildJobError()
    {
        $ticket             = 'ticket';
        $workflowId         = 'workflow-id';
        $workflowParameters = $this->getMock('\Serializable');

        $configuration = new Configuration($workflowId, $workflowParameters);

        $task = new Task();
        $task->setType(new TaskType());
        $task->getType()->setJobType('foobar');
        $task->getParameters(clone $workflowParameters);

        $job      = $this->createJob($ticket, 'workflow', $configuration);
        $childJob = $this->createJobInformation(Status::ERROR(), 'task-ticket', 'foobar');

        $job->expects($this->once())
            ->method('getResponseBody')
            ->willReturn(new ErrorResponse('foobar', 100));

        $job->expects($this->any())
            ->method('IsTriggeredByCallback')
            ->will($this->returnValue(true));

        $job->expects($this->any())
            ->method('getCallerJob')
            ->will($this->returnValue($childJob));

        try
        {
            $this->subject->execute($job);
        }
        catch(TerminateException $e)
        {
            $this->assertEquals('foobar', $e->getMessage());
            $this->assertEquals(100, $e->getCode());
        }
    }

    /**
     * @expectedException \Abc\Bundle\JobBundle\Job\Exception\TerminateException
     */
    public function testExecuteThrowsTerminateExceptionOnChildJobCanceled()
    {
        $ticket             = 'ticket';
        $workflowId         = 'workflow-id';
        $workflowParameters = $this->getMock('\Serializable');

        $configuration = new Configuration($workflowId, $workflowParameters);

        $task = new Task();
        $task->setType(new TaskType());
        $task->getType()->setJobType('foobar');
        $task->getParameters(clone $workflowParameters);

        $job      = $this->createJob($ticket, 'workflow', $configuration);
        $childJob = $this->createJobInformation(Status::CANCELLED(), 'task-ticket', 'foobar');

        $job->expects($this->any())
            ->method('IsTriggeredByCallback')
            ->will($this->returnValue(true));

        $job->expects($this->any())
            ->method('getCallerJob')
            ->will($this->returnValue($childJob));

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

    /**
     * @param Status $status
     * @param mixed  $ticket
     * @param mixed  $type
     * @param mixed  $parameter
     * @return Job|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createJobInformation(Status $status, $ticket = null, $type = null, $parameter = null)
    {
        $job = $this->getMock('Abc\Bundle\JobBundle\Job\JobInformation');

        $job->expects($this->any())
            ->method('getTicket')
            ->will($this->returnValue($ticket));

        $job->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $job->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue($status));

        $job->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue($parameter));

        return $job;
    }
}