<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Form\Type;

use Abc\Bundle\WorkflowBundle\Form\Type\ScheduleType;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class ScheduleTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var ScheduleType */
    private $subject;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->subject = new ScheduleType($this->container);
    }

    public function testBuildForm()
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
        $dataClass = 'Abc\Bundle\WorkflowBundle\Entity\Schedule';

        $this->assertTrue(class_exists($dataClass));

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(array('data_class' => $dataClass));

        $this->subject->setDefaultOptions($resolver);
    }
}