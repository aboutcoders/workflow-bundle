<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Integration;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceTest extends KernelTestCase
{

    /** @var EntityManager */
    private $em;
    /** @var Application */
    private $application;

    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();;

        $this->container   = static::$kernel->getContainer();
        $this->application = new Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(false);
    }

    public function testWorkflowManager()
    {
        $subject = $this->container->get('abc_workflow.workflow_manager');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface', $subject);
    }

    public function testWorkflowExecutionManager()
    {
        $subject = $this->container->get('abc_workflow.workflow_execution_manager');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Model\WorkflowExecutionManagerInterface', $subject);
    }

    public function testMailerForm()
    {
        $subject = $this->container->get('abc.workflow.task.form.mailer');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Form\Task\MailerType', $subject);
    }

    public function testWorkflowExecutable()
    {
        $subject = $this->container->get('abc.workflow.executable.workflow');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Executable\WorkflowExecutable', $subject);
    }

    public function testMenuBuilder()
    {
        $subject = $this->container->get('abc.workflow.menu.builder');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Menu\MenuBuilder', $subject);
    }

    public function testMainMenu()
    {
        $this->container->enterScope('request');
        $this->container->set('request', new \Symfony\Component\HttpFoundation\Request(), 'request');
        $subject = $this->container->get('abc.workflow.menu.main');

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $subject);
    }
}