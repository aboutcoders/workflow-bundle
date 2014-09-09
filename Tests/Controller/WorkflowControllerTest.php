<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkflowControllerTest extends WebTestCase
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
    }

    public function testNew()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/workflow/new');

        $submitButton = $crawler->selectButton('Save');

        $form = $submitButton->form();

        $client->submit(
            $form,
            array(
                'abc_workflow_bundle_workflow[name]' => 'New Workflow',
            )
        );

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals('http://localhost/workflow/1', $client->getRequest()->getUri());
    }


    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            '/workflow/'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('New Workflow', $crawler->html());
    }

    public function testEdit()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            '/workflow/'
        );

        $editLink = $crawler->selectLink('Edit')->link();

        $crawler = $client->click($editLink);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $submitButton = $crawler->selectButton('Save');

        $form = $submitButton->form();

        $client->submit(
            $form,
            array(
                'abc_workflow_bundle_workflow[name]' => 'Updated Workflow',
            )
        );

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals('http://localhost/workflow/1/edit', $client->getRequest()->getUri());

        $crawler = $client->request(
            'GET',
            '/workflow/'
        );

        $this->assertContains('Updated Workflow', $crawler->html());
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            '/workflow/'
        );

        $editLink = $crawler->selectLink('Show')->link();

        $crawler = $client->click($editLink);

        $deleteButton = $crawler->selectButton('Delete');

        $form = $deleteButton->form();

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals('http://localhost/workflow/', $client->getRequest()->getUri());

        $this->assertNotContains('Updated Workflow', $crawler->html());
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