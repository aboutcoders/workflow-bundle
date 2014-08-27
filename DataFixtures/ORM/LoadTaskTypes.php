<?php

namespace Abc\Bundle\WorkflowBundle\DataFixtures\ORM;

use Abc\Bundle\WorkflowBundle\Model\CategoryManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTaskTypes extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $taskTypeManager = $this->container->get('abc.workflow.task_type_manager');

        /** @var CategoryManagerInterface $categoryManager */
        $categoryManager = $this->container->get('abc.workflow.category_manager');

        $category = $categoryManager->create();
        $category->setName('General');
        $category->setIcon('asterisk');
        $categoryManager->update($category);

        $mailerType = $taskTypeManager->create();
        $mailerType->setName('Send mail');
        $mailerType->setJobType('mailer');
        $mailerType->setFormServiceName('abc.workflow.task.form.mailer');
        $mailerType->setCategory($category);
        $mailerType->setIcon('send');
        $taskTypeManager->update($mailerType);

        $distributeArtifactsType = $taskTypeManager->create();
        $distributeArtifactsType->setName('Distribute artifacts');
        $distributeArtifactsType->setJobType('workflow_distribute_artifacts');
        $distributeArtifactsType->setFormServiceName('abc.workflow.task.form.distribute_artifacts');
        $distributeArtifactsType->setCategory($category);
        $distributeArtifactsType->setIcon('transfer');
        $taskTypeManager->update($distributeArtifactsType);
    }
}