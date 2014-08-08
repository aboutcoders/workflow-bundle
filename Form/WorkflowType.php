<?php

namespace Abc\Bundle\WorkflowBundle\Form;

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
            'empty_value' => true
        ));
        $builder->add('disabled', null,
            array(
                'required'    => false,
                'widget_type' => 'inline',
                'label'       => 'Disable this workflow',
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
