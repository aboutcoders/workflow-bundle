<?php

namespace Abc\Bundle\WorkflowBundle\Form;

use Abc\Bundle\WorkflowBundle\Model\TaskTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{

    /** @var ContainerInterface */
    protected $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('workflowId', 'hidden');
        $builder->add('typeId', 'hidden');
        $builder->add('description', 'text', array('label' => 'Task description'));
        $builder->add('disabled', null,
            array(
                'required'    => false,
                'widget_type' => 'inline',
                'label'       => 'Disable this task',
            )
        );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $task = $event->getData();
                $form = $event->getForm();

                if($taskTypeForm = $this->buildTaskTypeForm($task->getType()))
                {
                    $form->add('parameters', $taskTypeForm);
                    if($taskTypeForm instanceof SchedulableTask)
                    {
                        $form->add('schedule', new ScheduleType());
                    }
                }
            });
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Abc\Bundle\WorkflowBundle\Entity\Task'
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'abc_bundle_workflowbundle_task';
    }

    /**
     * @param TaskTypeInterface $taskType
     * @return FormBuilderInterface|null
     * @throws ServiceNotFoundException When the service is not defined
     */
    private function buildTaskTypeForm($taskType)
    {
        if($taskType->getFormServiceName() != null)
        {
            return $this->container->get($taskType->getFormServiceName());
        }
    }
}
