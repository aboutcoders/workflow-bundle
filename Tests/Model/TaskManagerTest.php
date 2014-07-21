<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Model;


use Abc\Bundle\WorkflowBundle\Model\TaskManager;

class TaskManagerTest extends \PHPUnit_Framework_TestCase
{

    /** @var TaskManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $subject;

    public function setUp()
    {
        $this->subject = $this->getMockForAbstractClass('Abc\Bundle\WorkflowBundle\Model\TaskManager');
    }


    public function testCreate()
    {
        $this->subject->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue('Abc\Bundle\WorkflowBundle\Entity\Task'));

        $entity = $this->subject->create();

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Entity\Task', $entity);
    }
}