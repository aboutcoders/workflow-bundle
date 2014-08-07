<?php

namespace Abc\Bundle\WorkflowBundle\DataFixtures\ORM;

use Abc\Bundle\WorkflowBundle\Model\TaskTypeCategoryManagerInterface;
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
        $taskTypeManager = $this->container->get('abc.workflow.task_type_manager');

        /** @var TaskTypeCategoryManagerInterface $taskTypeCategoryManager */
        $taskTypeCategoryManager = $this->container->get('abc.workflow.task_type_category_manager');

        $category1 = $taskTypeCategoryManager->create();
        $category1->setName('General');
        $taskTypeCategoryManager->update($category1);
        $category1->setIcon('asterisk');
        $category2 = $taskTypeCategoryManager->create();
        $category2->setName('Transcoding');
        $category2->setIcon('retweet');
        $taskTypeCategoryManager->update($category2);
        $category3 = $taskTypeCategoryManager->create();
        $category3->setName('Export');
        $category3->setIcon('share-alt');
        $taskTypeCategoryManager->update($category3);

        $this->addReference('taskCategory-General', $category1);
        $this->addReference('taskCategory-Transcoding', $category2);
        $this->addReference('taskCategory-Export', $category3);


        $item1 = $taskTypeManager->create();
        $item1->setName('Send mail');
        $item1->setJobType('mailer');
        $item1->setFormServiceName('abc.workflow.task.form.mailer');
        $item1->setCategory($category1);
        $item1->setIcon('send');
        $taskTypeManager->update($item1);
    }
}