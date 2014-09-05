<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Model;


use Abc\Bundle\WorkflowBundle\Model\TaskTypeManager;

class TaskTypeManagerTest extends \PHPUnit_Framework_TestCase
{

    /** @var TaskTypeManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $subject;

    public function setUp()
    {
        $this->subject = $this->getMockForAbstractClass('Abc\Bundle\WorkflowBundle\Model\TaskTypeManager');
    }


    public function testCreate()
    {
        $this->subject->expects($this->any())
            ->method('getClass')
            ->willReturn('Abc\Bundle\WorkflowBundle\Entity\TaskType');

        $entity = $this->subject->create();

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Entity\TaskType', $entity);
    }
}