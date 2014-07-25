<?php

namespace Abc\Bundle\WorkflowBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class ScheduleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('expression', 'text', array('label' => 'Cron expression'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Abc\Bundle\WorkflowBundle\Entity\Schedule'
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'abc_bundle_workflowbundle_task';
    }
} 