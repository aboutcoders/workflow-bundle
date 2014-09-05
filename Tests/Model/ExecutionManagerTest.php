<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Model;

use Abc\Bundle\WorkflowBundle\Entity\Workflow;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManager;

class ExecutionManagerTest extends \PHPUnit_Framework_TestCase
{

    /** @var ExecutionManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $subject;

    public function setUp()
    {
        $this->subject = $this->getMockForAbstractClass('Abc\Bundle\WorkflowBundle\Model\ExecutionManager');
    }


    public function testCreate()
    {
        $this->subject->expects($this->any())
            ->method('getClass')
            ->willReturn('Abc\Bundle\WorkflowBundle\Entity\Execution');

        $entity = $this->subject->create('ABC', new Workflow());

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Entity\Execution', $entity);
    }
}
 