<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Integration;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceContainerTest extends KernelTestCase
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

    public function testTwigWorkflowExtension()
    {
        $subject = $this->container->get('abc.workflow.workflow_extension');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Twig\WorkflowExtension', $subject);
    }

    public function testWorkflowManager()
    {
        $subject = $this->container->get('abc.workflow.workflow_manager');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface', $subject);
    }

    public function testWorkflowExecutionManager()
    {
        $subject = $this->container->get('abc.workflow.execution_manager');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface', $subject);
    }

    public function testMailerForm()
    {
        $subject = $this->container->get('abc.workflow.task.form.mailer');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Form\Task\MailerType', $subject);
    }

    public function testWorkflowExecutable()
    {
        $subject = $this->container->get('abc.workflow.executable.workflow_executor');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Executable\WorkflowExecutor', $subject);
    }

    public function testJobListener()
    {
        $subject = $this->container->get('abc.workflow.job_listener');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Listener\JobListener', $subject);
    }

    public function testManager()
    {
        $subject = $this->container->get('abc.workflow.manager');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Workflow\ManagerInterface', $subject);
    }
}