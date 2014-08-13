<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Doctrine;


use Abc\Bundle\WorkflowBundle\Doctrine\TaskManager;
use Abc\Bundle\WorkflowBundle\Entity\Task;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class TaskManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    private $class;
    /** @var ClassMetadata|\PHPUnit_Framework_MockObject_MockObject */
    private $classMetaData;
    /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    private $objectManager;
    /** @var ObjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var TaskManager */
    private $subject;


    public function setUp()
    {
        $this->class         = 'Abc\Bundle\WorkflowBundle\Entity\Task';
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

        $this->subject = new TaskManager($this->objectManager, $this->class);
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

    public function testFindById()
    {
        $id = 1;
        $this->repository->expects($this->once())
            ->method('find')
            ->with($id);

        $this->subject->findById($id);
    }


    public function testFindWorkflowTasks()
    {
        $id = 1;
        $this->repository->expects($this->once())
            ->method('findBy')
            ->with(array('workflowId' => $id), array('position' => 'ASC'));

        $this->subject->findWorkflowTasks($id);
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