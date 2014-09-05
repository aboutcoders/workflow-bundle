<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Twig;


use Abc\Bundle\WorkflowBundle\Model\CategoryManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\Workflow;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Twig\WorkflowExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class WorkflowExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $container;
    /** @var TranslatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $translator;
    /** @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $engine;
    /** @var CategoryManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $categoryManager;
    /** @var ExecutionManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $executionManager;
    /** @var TaskManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $taskManager;
    /** @var TaskTypeManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $taskTypeManager;
    /** @var WorkflowManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $workflowManager;

    /** @var WorkflowExtension */
    private $subject;

    public function setUp()
    {
        $this->container        = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->translator       = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->engine           = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $this->executionManager = $this->getMock('Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface');
        $this->categoryManager  = $this->getMock('Abc\Bundle\WorkflowBundle\Model\CategoryManagerInterface');
        $this->taskManager      = $this->getMock('Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface');
        $this->taskTypeManager  = $this->getMock('Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface');
        $this->workflowManager  = $this->getMock('Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface');

        $this->subject = new WorkflowExtension($this->container, $this->translator);
    }

    public function testGetName()
    {
        $this->assertEquals('workflow_extension', $this->subject->getName());
    }

    public function testGetFunctions()
    {
        $functions = $this->subject->getFunctions();

        $this->assertArrayHasKey('workflow_configuration', $functions);
        $this->assertInstanceOf('\Twig_Function_Method', $functions['workflow_configuration']);
        $this->assertArrayHasKey('workflow_history', $functions);
        $this->assertInstanceOf('\Twig_Function_Method', $functions['workflow_history']);
    }

    public function testWorkflowConfiguration()
    {
        $types      = array('types');
        $categories = array('categories');
        $tasks      = array('tasks');
        $workflowId = 'workflow-id';
        $workflow   = new Workflow();
        $workflow->setId($workflowId);

        $this->setUpContainer();

        $this->workflowManager->expects($this->once())
            ->method('findById')
            ->with($workflowId)
            ->willReturn($workflow);

        $this->taskTypeManager->expects($this->once())
            ->method('findAll')
            ->willReturn($types);

        $this->categoryManager->expects($this->once())
            ->method('findAll')
            ->willReturn($categories);

        $this->taskManager->expects($this->once())
            ->method('findWorkflowTasks')
            ->with($workflowId)
            ->willReturn($tasks);

        $this->engine->expects($this->once())
            ->method('render')
            ->with(
                'AbcWorkflowBundle:Task:configureWorkflow.html.twig',
                array(
                    'entity' => $workflow,
                    'types' => $types,
                    'tasks' => $tasks,
                    'categories' => $categories,
                )
            );

        $this->subject->workflowConfiguration($workflowId);
    }

    public function testWorkflowHistory()
    {
        $executions = array('executions');
        $workflowId = 'workflow-id';
        $workflow   = new Workflow();
        $workflow->setId($workflowId);

        $this->setUpContainer();

        $this->executionManager->expects($this->once())
            ->method('findHistory')
            ->with($workflowId)
            ->willReturn($executions);

        $this->engine->expects($this->once())
            ->method('render')
            ->with(
                'AbcWorkflowBundle:Execution:workflowHistory.html.twig',
                array(
                    'workflow' => $workflow,
                    'executions' => $executions,
                )
            );

        $this->subject->workflowHistory($workflow);
    }

    private function setUpContainer()
    {
        $engine           = $this->engine;
        $categoryManager  = $this->categoryManager;
        $executionManager = $this->executionManager;
        $taskManager      = $this->taskManager;
        $taskTypeManager  = $this->taskTypeManager;
        $workflowManager  = $this->workflowManager;

        $this->container->expects($this->any())
            ->method('get')
            ->willReturnCallback(
                function ($key) use ($engine, $categoryManager, $executionManager, $taskManager, $taskTypeManager, $workflowManager)
                {
                    switch($key)
                    {
                        case 'templating':
                            return $engine;
                        case 'abc.workflow.category_manager':
                            return $categoryManager;
                        case 'abc.workflow.execution_manager':
                            return $executionManager;
                        case 'abc.workflow.task_manager':
                            return $taskManager;
                        case 'abc.workflow.task_type_manager':
                            return $taskTypeManager;
                        case 'abc.workflow.workflow_manager':
                            return $workflowManager;
                        default:
                            throw new \InvalidArgumentException(sprintf('The service "%s" is not configured', $key));
                    }
                }
            );
    }
}