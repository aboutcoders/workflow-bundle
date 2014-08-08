<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Integration;

use Abc\Bundle\JobBundle\Job\Context\Context;
use Abc\Bundle\JobBundle\Model\Job;
use Abc\Bundle\WorkflowBundle\Listener\WorkflowFilesystemListener;
use Abc\Bundle\WorkflowBundle\Model\Workflow;
use Abc\File\FilesystemInterface;
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
        $subject = $this->container->get('abc.workflow.executable.workflow');

        $this->assertInstanceOf('Abc\Bundle\WorkflowBundle\Executable\WorkflowExecutable', $subject);
    }
}