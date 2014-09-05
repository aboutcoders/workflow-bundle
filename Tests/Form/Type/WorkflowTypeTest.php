<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Form\Type;

use Abc\Bundle\WorkflowBundle\Form\Type\WorkflowType;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class WorkflowTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var WorkflowType */
    private $subject;

    public function setUp()
    {
        $this->subject = new WorkflowType();
    }

    public function testBuildForm()
    {
        $builder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $options = array();

        $this->subject->buildForm($builder, $options);
    }

    public function testGetName()
    {
        $this->assertEquals('abc_workflow_bundle_workflow', $this->subject->getName());
    }

    public function testSetDefaultOptions()
    {
        $resolver  = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $dataClass = 'Abc\Bundle\WorkflowBundle\Entity\Workflow';

        $this->assertTrue(class_exists($dataClass));

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(array('data_class' => $dataClass));

        $this->subject->setDefaultOptions($resolver);
    }
}