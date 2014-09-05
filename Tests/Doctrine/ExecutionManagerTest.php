<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Doctrine;

use Abc\Bundle\JobBundle\Entity\Job;
use Abc\Bundle\JobBundle\Job\Report\Report;
use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\SequenceBundle\Model\SequenceManagerInterface;
use Abc\Bundle\WorkflowBundle\Doctrine\ExecutionManager;
use Abc\Bundle\WorkflowBundle\Entity\Execution;
use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Workflow\Configuration;
use Abc\Bundle\WorkflowBundle\Workflow\ManagerInterface;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class ExecutionManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var SequenceManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $sequenceManager;
    /** @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $manager;
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
        $this->class           = 'Abc\Bundle\WorkflowBundle\\Entity\Execution';
        $this->classMetaData   = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->objectManager   = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository      = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->manager         = $this->getMock('Abc\Bundle\WorkflowBundle\Workflow\ManagerInterface');
        $this->sequenceManager = $this->getMock('Abc\Bundle\SequenceBundle\Model\SequenceManagerInterface');

        $this->objectManager->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($this->classMetaData);

        $this->classMetaData->expects($this->any())
            ->method('getName')
            ->willReturn($this->class);

        $this->objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repository);

        $this->subject = new ExecutionManager(
            $this->objectManager,
            $this->class,
            $this->sequenceManager);

        $this->subject->setManager($this->manager);
    }

    public function testGetClass()
    {
        $this->assertEquals($this->class, $this->subject->getClass());
    }


    public function testUpdate()
    {
        $entity = $this->subject->create('ABC', new Workflow());

        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->objectManager->expects($this->once())
            ->method('flush');

        $this->subject->update($entity);
    }


    public function testUpdateWithFlush()
    {
        $entity = $this->subject->create('ABC', new Workflow());

        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->objectManager->expects($this->never())
            ->method('flush');

        $this->subject->update($entity, false);
    }


    public function testDelete()
    {
        $entity = $this->subject->create('ABC', new Workflow());

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
        $job1->setCreatedAt(new \DateTime);
        $job2 = new Job();
        $job2->setStatus(Status::CANCELLED());
        $job2->setCreatedAt(new \DateTime);
        $report1 = new Report($job1);
        $report2 = new Report($job2);

        $this->manager->expects($this->at(0))
            ->method('getReport')
            ->with($ticket1)
            ->willReturn($report1);
        $this->manager->expects($this->at(1))
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
        $execution1->setStatus(Status::CANCELLED());

        $execution2 = new Execution();
        $execution2->setTicket($ticket2);
        $execution2->setExecutionTime(321);
        $execution2->setStatus(Status::PROCESSED());
        $executions = array();

        $executions[] = $execution1;
        $executions[] = $execution2;

        $this->manager->expects($this->never())
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
        $job1->setCreatedAt(new \DateTime('2010-01-01 00:00:00'));
        $job1->setTerminatedAt(new \DateTime('2010-01-01 00:00:05'));
        $report1 = new Report($job1);

        $this->manager->expects($this->at(0))
            ->method('getReport')
            ->with($ticket)
            ->willReturn($report1);

        $result = $this->subject->findById($ticket);

        $this->assertEquals(Status::REQUESTED(), $result->getStatus());
        $this->assertEquals(5, $result->getExecutionTime());
    }

    public function testFindByIdWithStoredData()
    {
        $ticket = 'foo';

        $execution = new Execution();
        $execution->setTicket($ticket);
        $execution->setExecutionTime(50);
        $execution->setStatus(Status::PROCESSED());

        $this->repository->expects($this->once())
            ->method('find')
            ->with($ticket)
            ->willReturn($execution);

        $this->manager->expects($this->never())
            ->method('getReport');

        $result = $this->subject->findById($ticket);

        $this->assertEquals($execution->getStatus(), $result->getStatus());
        $this->assertEquals($execution->getExecutionTime(), $result->getExecutionTime());
    }
}