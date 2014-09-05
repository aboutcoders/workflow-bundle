<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Form\Type;

use Abc\Bundle\WorkflowBundle\Entity\Task;
use Abc\Bundle\WorkflowBundle\Form\Type\TaskType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class TaskTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $container;
    /** @var TaskType */
    private $subject;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->subject = new TaskType($this->container);
    }

    public function testBuildForm()
    {
        $builder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $options = array('data' => new Task());

        $this->subject->buildForm($builder, $options);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildFormThrowsInvalidArgumentExceptionIfDataNotSet()
    {
        $builder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $options = array();

        $this->subject->buildForm($builder, $options);
    }

    public function testGetName()
    {
        $this->assertEquals('abc_bundle_workflowbundle_task', $this->subject->getName());
    }

    public function testSetDefaultOptions()
    {
        $resolver  = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $dataClass = 'Abc\Bundle\WorkflowBundle\Entity\Task';

        $this->assertTrue(class_exists($dataClass));

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(array('data_class' => $dataClass));

        $this->subject->setDefaultOptions($resolver);
    }
}
 