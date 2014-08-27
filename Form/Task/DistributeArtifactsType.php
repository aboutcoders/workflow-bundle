<?php

namespace Abc\Bundle\WorkflowBundle\Form\Task;

use Abc\Bundle\FileDistributionBundle\Model\DefinitionManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DistributeArtifactsType extends AbstractType
{

    /** @var  DefinitionManagerInterface */
    protected $definitionManager;

    function __construct(DefinitionManagerInterface $definitionManager)
    {
        $this->definitionManager = $definitionManager;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('workspaceLifetime', 'integer', array('label' => 'Remove workspace after X days', 'required' => false));
        $builder->add('definitionId', 'choice', array('choices' => $this->definitionManager->getFilesystemsWithPublicUrl()));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'abc_bundle_target_platform_distribute_artifacts';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Abc\Bundle\WorkflowBundle\Executable\DistributeArtifactsParameter'
            ));
    }
}