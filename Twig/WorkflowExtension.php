<?php
namespace Abc\Bundle\WorkflowBundle\Twig;

use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
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
            'workflow_history'       => new \Twig_Function_Method($this, 'workflowHistory', array('is_safe' => array('html'))),
        );
    }

    public function workflowConfiguration(WorkflowInterface $workflow)
    {
        $taskManager    = $this->getTaskManager();
        $tasTypeManager = $this->getTaskTypeManager();

        $types = $tasTypeManager->findAll();
        $tasks = $taskManager->findWorkflowTasks($workflow->getId());

        return $this->container->get('templating')
            ->render("AbcWorkflowBundle:Task:configureWorkflow.html.twig",
                array(
                    'entity' => $workflow,
                    'types'  => $types,
                    'tasks'  => $tasks
                )
            );
    }

    public function workflowHistory(WorkflowInterface $workflow)
    {
        $executionManager = $this->getExecutionManager();
        $executions       = $executionManager->findHistory($workflow->getId());

        return $this->container->get('templating')
            ->render("AbcWorkflowBundle:Task:workflowHistory.html.twig",
                array(
                    'workflow'   => $workflow,
                    'executions' => $executions,
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

    /**
     * @return ExecutionManagerInterface
     */
    private function getExecutionManager()
    {
        return $this->container->get('abc.workflow.execution_manager');
    }
} 