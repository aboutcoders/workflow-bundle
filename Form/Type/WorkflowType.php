<?php

namespace Abc\Bundle\WorkflowBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkflowType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('description');
        $builder->add('category', null, array(
            'empty_value' => ''
        ));
        $builder->add('disabled', null,
            array(
                'required'    => false,
                'widget_type' => 'inline',
                'label'       => 'Disable this workflow',
            )
        );
        $builder->add('createDirectory', null,
            array(
                'required'    => false,
                'widget_type' => 'inline',
                'label'       => 'Create working directory for execution',
            )
        );
        $builder->add('removeDirectory', null,
            array(
                'required'    => false,
                'widget_type' => 'inline',
                'label'       => 'Remove working directory after execution',
            )
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Abc\Bundle\WorkflowBundle\Entity\Workflow'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'abc_workflow_bundle_workflow';
    }
}
