<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Model;


use Abc\Bundle\WorkflowBundle\Model\CategoryManager;

class CategoryManagerTest extends \PHPUnit_Framework_TestCase
{

    /** @var CategoryManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $subject;

    public function setUp()
    {
        $this->subject = $this->getMockForAbstractClass('Abc\Bundle\WorkflowBundle\Model\CategoryManager');
    }


    public function testCreate()
    {
        $this->subject->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue('Abc\Bundle\WorkflowBundle\Entity\Category'));

        $entity = $this->subject->create();

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Entity\Category', $entity);
    }
}