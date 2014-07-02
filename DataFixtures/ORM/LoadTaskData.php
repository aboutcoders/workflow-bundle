<?php

namespace Abc\Bundle\WorkflowBundle\DataFixtures\ORM;

use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTaskData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function getOrder()
    {
        return 1;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        /** @var TaskTypeManagerInterface $taskTypeManager */
        $taskTypeManager = $this->container->get('abc_workflow.task_type_manager');

        $item1 = $taskTypeManager->create();
        $item1->setName('Mailer');
        $item1->setFormServiceName('abc.workflow.task.form.mailer');
        $taskTypeManager->update($item1);

    }

}
