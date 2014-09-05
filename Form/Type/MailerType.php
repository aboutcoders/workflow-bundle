<?php

namespace Abc\Bundle\WorkflowBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailerType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('to', 'email', array(
            'label_render'         => true,
            'widget_addon_prepend' => array('text' => '@'),
            'attr'                 => array(
                'placeholder' => 'Email address of receiver'
            )
        ));
        $builder->add('from', 'email', array(
            'label_render'         => true,
            'widget_addon_prepend' => array('text' => '@'),
            'attr'                 => array(
                'placeholder' => 'Email address of sender'
            )
        ));
        $builder->add('fromName', 'text', array(
            'attr' => array(
                'placeholder' => 'From name'
            )));

        $builder->add('subject', 'text', array(
            'attr' => array(
                'placeholder' => 'Message subject'
            )));
        $builder->add('message', 'textarea', array(
            'attr' => array(
                'placeholder' => 'Message body'
            )));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'abc_bundle_workflowbundle_mailer';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Abc\Bundle\JobBundle\Executable\SwiftMailerParameter'
        ));
    }
}
