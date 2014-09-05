<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Form\Type;

use Abc\Bundle\WorkflowBundle\Form\Type\MailerType;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class MailerTypeTest extends \PHPUnit_Framework_TestCase
{

    /** @var MailerType */
    private $subject;

    public function setUp()
    {
        $this->subject = new MailerType();
    }

    public function testBuildForm()
    {
        $builder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $options = array();

        $this->subject->buildForm($builder, $options);
    }

    public function testGetName()
    {
        $this->assertEquals('abc_bundle_workflowbundle_mailer', $this->subject->getName());
    }

    public function testSetDefaultOptions()
    {
        $resolver  = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $dataClass = 'Abc\Bundle\JobBundle\Executable\SwiftMailerParameter';

        $this->assertTrue(class_exists($dataClass));

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(array('data_class' => $dataClass));

        $this->subject->setDefaultOptions($resolver);
    }
}
 