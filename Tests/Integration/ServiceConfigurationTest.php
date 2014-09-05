<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Integration;

use Abc\Bundle\JobBundle\Event\JobEvents;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceConfigurationTest extends KernelTestCase
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
            array('abc.workflow.task.form.mailer', 'Abc\Bundle\WorkflowBundle\Form\Type\MailerType'),
            array('abc.workflow.task.form.distribute_artifacts', 'Abc\Bundle\WorkflowBundle\Form\Type\DistributeArtifactsType'),
            array('abc.workflow.executable.workflow_executor', 'Abc\Bundle\WorkflowBundle\Executable\WorkflowExecutor'),
            array('abc.workflow.executable.cleanup_directory', 'Abc\Bundle\WorkflowBundle\Executable\CleanupDirectoryExecutable'),
            array('abc.workflow.executable.distribute_artifacts', 'Abc\Bundle\WorkflowBundle\Executable\ArtifactDistributor'),
            array('abc.workflow.job_listener', 'Abc\Bundle\WorkflowBundle\Listener\JobListener'),
            array('abc.workflow.manager', 'Abc\Bundle\WorkflowBundle\Workflow\ManagerInterface'),
        );
    }


    public function testJobListenerListensToJobPrepare()
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->container->get('event_dispatcher');
        /** @var \Abc\Bundle\WorkflowBundle\Listener\JobListener|\PHPUnit_Framework_MockObject_MockObject $listener */
        $listener = $this->getMockBuilder('Abc\Bundle\WorkflowBundle\Listener\JobListener')->disableOriginalConstructor()->getMock();
        /** @var \Abc\Bundle\JobBundle\Event\JobEvent|\PHPUnit_Framework_MockObject_MockObject $listener */
        $event = $this->getMockBuilder('Abc\Bundle\JobBundle\Event\JobEvent')->disableOriginalConstructor()->getMock();

        // disable listener registered by the job bundle
        $this->container->set('abc.job.listener.job', $this->getMockBuilder('Abc\Bundle\JobBundle\Listener\JobListener')->disableOriginalConstructor()->getMock());

        $this->container->set('abc.workflow.job_listener', $listener);

        $listener->expects($this->once())
            ->method('onPrepare')
            ->with($event);

        $dispatcher->dispatch(JobEvents::JOB_PREPARE, $event);
    }

    public function testJobListenerListensToJobTerminated()
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->container->get('event_dispatcher');
        /** @var \Abc\Bundle\WorkflowBundle\Listener\JobListener|\PHPUnit_Framework_MockObject_MockObject $listener */
        $listener = $this->getMockBuilder('Abc\Bundle\WorkflowBundle\Listener\JobListener')->disableOriginalConstructor()->getMock();
        /** @var \Abc\Bundle\JobBundle\Event\ReportEvent|\PHPUnit_Framework_MockObject_MockObject $listener */
        $event = $this->getMockBuilder('Abc\Bundle\JobBundle\Event\ReportEvent')->disableOriginalConstructor()->getMock();

        // disable listener registered by the job bundle
        $this->container->set('abc.job.listener.job', $this->getMockBuilder('Abc\Bundle\JobBundle\Listener\JobListener')->disableOriginalConstructor()->getMock());

        $this->container->set('abc.workflow.job_listener', $listener);

        $listener->expects($this->once())
            ->method('onTerminated')
            ->with($event);

        $dispatcher->dispatch(JobEvents::JOB_TERMINATED, $event);
    }
}