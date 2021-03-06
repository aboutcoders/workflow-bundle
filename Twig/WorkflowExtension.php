<?php

namespace Abc\Bundle\WorkflowBundle\Twig;

use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\CategoryManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
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
            'workflow_history' => new \Twig_Function_Method($this, 'workflowHistory', array('is_safe' => array('html'))),
        );
    }

    /**
     * @param $workflowId
     * @return int The workflow id
     */
    public function workflowConfiguration($workflowId)
    {
        $workflow               = $this->getWorkflowManager()->findById($workflowId);
        $taskManager            = $this->getTaskManager();
        $taskTypeManager         = $this->getTaskTypeManager();
        $tasTypeCategoryManager = $this->getCategoryManager();

        $types      = $taskTypeManager->findAll();
        $categories = $tasTypeCategoryManager->findAll();
        $tasks      = $taskManager->findWorkflowTasks($workflow->getId());

        return $this->container->get('templating')
            ->render(
                "AbcWorkflowBundle:Task:configureWorkflow.html.twig",
                array(
                    'entity' => $workflow,
                    'types' => $types,
                    'tasks' => $tasks,
                    'categories' => $categories,
                )
            );
    }

    public function workflowHistory(WorkflowInterface $workflow)
    {
        $executionManager = $this->getExecutionManager();
        $executions       = $executionManager->findHistory($workflow->getId());

        return $this->container->get('templating')
            ->render(
                "AbcWorkflowBundle:Execution:workflowHistory.html.twig",
                array(
                    'workflow' => $workflow,
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
     * @return CategoryManagerInterface
     */
    protected function getCategoryManager()
    {
        return $this->container->get('abc.workflow.category_manager');
    }

    /**
     * @return ExecutionManagerInterface
     */
    private function getExecutionManager()
    {
        return $this->container->get('abc.workflow.execution_manager');
    }

    /**
     * @return WorkflowManagerInterface
     */
    private function getWorkflowManager()
    {
        return $this->container->get('abc.workflow.workflow_manager');
    }
} 