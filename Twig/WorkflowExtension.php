<?php
namespace Abc\Bundle\WorkflowBundle\Twig;

use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class WorkflowExtension extends \Twig_Extension
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->container  = $container;
    }

    public function getFunctions()
    {
        return array(
            'workflow_configuration' => new \Twig_Function_Method($this, 'workflowConfiguration', array('is_safe' => array('html'))),
        );
    }

    public function workflowConfiguration($entity)
    {
        $taskManager    = $this->getTaskManager();
        $tasTypeManager = $this->getTaskTypeManager();

        $types = $tasTypeManager->findAll();
        $tasks = $taskManager->findWorkflowTasks($entity->getId());

        return $this->container->get('templating')
            ->render("AbcWorkflowBundle:Task:configureWorkflow.html.twig",
                array(
                    'entity' => $entity,
                    'types'  => $types,
                    'tasks'  => $tasks
                )
            );
    }

    public function getName()
    {
        return 'workflow_extension';
    }

    /**
     * @return TaskManagerInterface
     */
    protected function getTaskManager()
    {
        return $this->container->get('abc.workflow.task_manager');
    }

    /**
     * @return TaskTypeManagerInterface
     */
    protected function getTaskTypeManager()
    {
        return $this->container->get('abc.workflow.task_type_manager');
    }
} 