<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Doctrine;

use Abc\Bundle\WorkflowBundle\Doctrine\CategoryManager;
use Abc\Bundle\WorkflowBundle\Model\Category;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class CategoryManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    private $class;
    /** @var ClassMetadata|\PHPUnit_Framework_MockObject_MockObject */
    private $classMetaData;
    /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    private $objectManager;
    /** @var ObjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var CategoryManager */
    private $subject;


    public function setUp()
    {
        $this->class         = 'Abc\Bundle\WorkflowBundle\Entity\Category';
        $this->classMetaData = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository    = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->objectManager->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($this->classMetaData);

        $this->classMetaData->expects($this->any())
            ->method('getName')
            ->willReturn($this->class);

        $this->objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repository);

        $this->subject = new CategoryManager($this->objectManager, $this->class);
    }


    public function testGetClass()
    {
        $this->assertEquals($this->class, $this->subject->getClass());
    }

    public function testExistsWithMatchFound()
    {
        $this->repository->expects($this->once())
            ->method('findBy')
            ->with(array('name' => 'foobar'))
            ->willReturn(array(new Category()));

        $this->assertTrue($this->subject->exists('foobar'));
    }

    public function testExistsWithNoMatchFound()
    {
        $this->repository->expects($this->once())
            ->method('findBy')
            ->with(array('name' => 'foobar'))
            ->willReturn(array());

        $this->assertFalse($this->subject->exists('foobar'));
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


    public function testFindBy()
    {
        $criteria = array('foo');

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($criteria);

        $this->subject->findBy($criteria);
    }

    public function testFindById()
    {
        $id = 1;

        $this->repository->expects($this->once())
            ->method('find')
            ->with($id);

        $this->subject->findById($id);
    }

    public function testFindBySlug()
    {
        $slug     = 'ABC';
        $criteria = array('slug' => $slug);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria);

        $this->subject->findBySlug($slug);
    }
}