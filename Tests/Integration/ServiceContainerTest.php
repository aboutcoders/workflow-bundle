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

    /**
     * @param string $service
     * @param string $type
     * @dataProvider getServices
     */
    public function testGetFromContainer($service, $type)
    {
        $subject = $this->container->get($service);

        $this->assertInstanceOf($type, $subject);
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return array(
            array('abc.workflow.workflow_extension', 'Abc\Bundle\WorkflowBundle\Twig\WorkflowExtension'),
            array('abc.workflow.workflow_manager', 'Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface'),
            array('abc.workflow.execution_manager', 'Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface'),
            array('abc.workflow.task.form.mailer', 'Abc\Bundle\WorkflowBundle\Form\Task\MailerType'),
            array('abc.workflow.executable.workflow_executor', 'Abc\Bundle\WorkflowBundle\Executable\WorkflowExecutor'),
            array('abc.workflow.executable.cleanup_directory', 'Abc\Bundle\WorkflowBundle\Executable\CleanupDirectoryExecutable'),
            array('abc.workflow.job_listener', 'Abc\Bundle\WorkflowBundle\Listener\JobListener'),
            array('abc.workflow.manager', 'Abc\Bundle\WorkflowBundle\Workflow\ManagerInterface'),
        );
    }
}