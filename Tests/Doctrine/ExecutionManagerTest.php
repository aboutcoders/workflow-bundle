<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Doctrine;

use Abc\Bundle\JobBundle\Entity\Job;
use Abc\Bundle\JobBundle\Job\ManagerInterface;
use Abc\Bundle\JobBundle\Job\Report\Report;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\WorkflowBundle\Doctrine\ExecutionManager;
use Abc\Bundle\WorkflowBundle\Entity\Execution;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class ExecutionManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $jobManager;
    /** @var string */
    private $class;
    /** @var ClassMetadata|\PHPUnit_Framework_MockObject_MockObject */
    private $classMetaData;
    /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    private $objectManager;
    /** @var ObjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $repository;
    /** @var ExecutionManager */
    private $subject;


    public function setUp()
    {
        $this->class         = 'Abc\Bundle\WorkflowBundle\\Entity\Execution';
        $this->classMetaData = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository    = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->jobManager    = $this->getMock('Abc\Bundle\JobBundle\Job\ManagerInterface');

        $this->objectManager->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($this->classMetaData));

        $this->classMetaData->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($this->class));

        $this->objectManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));

        $this->subject = new ExecutionManager($this->objectManager, $this->class, $this->jobManager);
    }


    public function testGetClass()
    {
        $this->assertEquals($this->class, $this->subject->getClass());
    }


    public function testUpdate()
    {
        $entity = $this->subject->create();

        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->objectManager->expects($this->once())
            ->method('flush');

        $this->subject->update($entity);
    }


    public function testUpdateWithFlush()
    {
        $entity = $this->subject->create();

        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->objectManager->expects($this->never())
            ->method('flush');

        $this->subject->update($entity, false);
    }


    public function testDelete()
    {
        $entity = $this->subject->create();

        $this->objectManager->expects($this->once())
            ->method('remove')
            ->with($entity);

        $this->objectManager->expects($this->once())
            ->method('flush');

        $this->subject->delete($entity);
    }


    public function testFindAll()
    {
        $this->repository->expects($this->once())
            ->method('findAll');

        $this->subject->findAll();
    }

    public function testFindHistoryWithReportData()
    {
        $workflowId = 1;
        $criteria   = array('workflowId' => $workflowId);
        $order      = array('createdAt' => 'DESC');
        $limit      = 20;
        $ticket1    = 'ABC1';
        $ticket2    = 'ABC2';
        $execution1 = new Execution();
        $execution1->setTicket($ticket1);
        $execution2 = new Execution();
        $execution2->setTicket($ticket2);
        $executions = array();

        $executions[] = $execution1;
        $executions[] = $execution2;

        $job1 = new Job();
        $job1->setStatus(Status::REQUESTED());
        $job2 = new Job();
        $job2->setStatus(Status::CANCELLED());
        $report1 = new Report($job1);
        $report2 = new Report($job2);

        $this->jobManager->expects($this->at(0))
            ->method('getReport')
            ->with($ticket1)
            ->willReturn($report1);
        $this->jobManager->expects($this->at(1))
            ->method('getReport')
            ->with($ticket2)
            ->willReturn($report2);

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($criteria, $order, $limit)
            ->willReturn($executions);

        $result = $this->subject->findHistory($workflowId);
        $this->assertCount(2, $result);
    }

    public function testFindHistoryWithStoredData()
    {
        $workflowId = 1;
        $criteria   = array('workflowId' => $workflowId);
        $order      = array('createdAt' => 'DESC');
        $limit      = 20;
        $ticket1    = 'ABC1';
        $ticket2    = 'ABC2';
        $execution1 = new Execution();
        $execution1->setTicket($ticket1);
        $execution1->setExecutionTime(123);
        $execution1->setStatus(Status::REQUESTED());
        $execution2 = new Execution();
        $execution2->setTicket($ticket2);
        $execution2->setExecutionTime(321);
        $execution2->setStatus(Status::CANCELLED());
        $executions = array();

        $executions[] = $execution1;
        $executions[] = $execution2;

        $this->jobManager->expects($this->never())
            ->method('getReport');

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($criteria, $order, $limit)
            ->willReturn($executions);

        $result = $this->subject->findHistory($workflowId);
        $this->assertCount(2, $result);
    }

    public function testFindBy()
    {
        $criteria = array('foo');

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($criteria);

        $this->subject->findBy($criteria);
    }

    public function testFindOneBy()
    {
        $criteria = array('foo');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria);

        $this->subject->findOneBy($criteria);
    }

    public function testFindByIdWithReportData()
    {
        $ticket = 'foo';

        $execution1 = new Execution();
        $execution1->setTicket($ticket);

        $this->repository->expects($this->once())
            ->method('find')
            ->with($ticket)
            ->willReturn($execution1);

        $job1 = new Job();
        $job1->setStatus(Status::REQUESTED());
        $job1->setExecutionTime(333);
        $report1 = new Report($job1);

        $this->jobManager->expects($this->at(0))
            ->method('getReport')
            ->with($ticket)
            ->willReturn($report1);

        $result = $this->subject->findById($ticket);

        $this->assertEquals(Status::REQUESTED(), $result->getStatus());
        $this->assertEquals(333, $result->getExecutionTime());
    }

    public function testFindByIdWithStoredData()
    {
        $ticket = 'foo';

        $execution1 = new Execution();
        $execution1->setTicket($ticket);
        $execution1->setExecutionTime(123);
        $execution1->setStatus(Status::REQUESTED());

        $this->repository->expects($this->once())
            ->method('find')
            ->with($ticket)
            ->willReturn($execution1);

        $this->jobManager->expects($this->never())
            ->method('getReport');

        $result = $this->subject->findById($ticket);

        $this->assertEquals(Status::REQUESTED(), $result->getStatus());
        $this->assertEquals(123, $result->getExecutionTime());
    }
}