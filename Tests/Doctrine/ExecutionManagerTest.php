<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Doctrine;

use Abc\Bundle\WorkflowBundle\Doctrine\ExecutionManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class ExecutionManagerTest extends \PHPUnit_Framework_TestCase
{

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

        $this->objectManager->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($this->classMetaData));

        $this->classMetaData->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($this->class));

        $this->objectManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));

        $this->subject = new ExecutionManager($this->objectManager, $this->class);
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

    public function testFindHistory()
    {
        $workflowId = 1;
        $criteria   = array('workflowId' => $workflowId);
        $order      = array('createdAt' => 'DESC');
        $limit      = 20;
        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($criteria, $order, $limit);

        $this->subject->findHistory($workflowId);
    }

    public function testFindBy()
    {
        $criteria = array('foo');

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($criteria);

        $this->subject->findBy($criteria);
    }
}