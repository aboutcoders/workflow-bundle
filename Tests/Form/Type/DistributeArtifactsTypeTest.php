<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Form\Type;

use Abc\Bundle\FileDistributionBundle\Model\DefinitionManagerInterface;
use Abc\Bundle\WorkflowBundle\Form\Type\DistributeArtifactsType;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class DistributeArtifactsTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var DefinitionManagerInterface */
    private $manager;
    /** @var DistributeArtifactsType */
    private $subject;

    public function setUp()
    {
        $this->manager = $this->getMock('Abc\Bundle\FileDistributionBundle\Model\DefinitionManagerInterface');
        $this->subject = new DistributeArtifactsType($this->manager);
    }

    public function testBuildForm()
    {
        $builder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $options = array();

        $this->subject->buildForm($builder, $options);
    }

    public function testGetName()
    {
        $this->assertEquals('abc_bundle_target_platform_distribute_artifacts', $this->subject->getName());
    }

    public function testSetDefaultOptions()
    {
        $resolver  = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $dataClass = 'Abc\Bundle\WorkflowBundle\Executable\DistributeArtifactsParameter';

        $this->assertTrue(class_exists($dataClass));

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(array('data_class' => $dataClass));

        $this->subject->setDefaultOptions($resolver);
    }
}