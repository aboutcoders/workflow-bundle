<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Controller;

use Abc\Bundle\WorkflowBundle\DataFixtures\ORM\LoadTaskTypes;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class TaskControllerTest extends WebTestCase
{
    /** @var Application */
    private static $application;

    public static function setUpBeforeClass()
    {
        self::bootKernel();

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        static::$application = $application;

        static::runConsole("doctrine:schema:drop", array("--force" => true));
        static::runConsole("doctrine:schema:update", array("--force" => true));

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $fixtures = new LoadTaskTypes();
        $fixtures->setContainer(static::$kernel->getContainer());
        $fixtures->load($em);

        /** @var WorkflowManagerInterface $manager */
        $manager = static::$kernel->getContainer()->get('abc.workflow.workflow_manager');

        $workflow = $manager->create();
        $workflow->setName('Test Workflow');
        $workflow->setDisabled(false);
        $manager->update($workflow);
    }

    public function testConfigure()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/workflow/');

        $editLink = $crawler->selectLink('Show')->link();

        $crawler = $client->click($editLink);

        $configureLink = $crawler->selectLink('Configure')->link();

        $client->click($configureLink);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/task/new/1/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $submitButton = $crawler->selectButton('Save');

        $form = $submitButton->form();

        $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEdit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/task/1/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $submitButton = $crawler->selectButton('Save');

        $form = $submitButton->form();

        $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('DELETE', '/task/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @param       $command
     * @param array $options
     * @return int
     */
    protected static function runConsole($command, array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options       = array_merge($options, array('command' => $command));

        return static::$application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }
}