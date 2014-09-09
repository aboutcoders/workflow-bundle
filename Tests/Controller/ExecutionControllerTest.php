<?php


namespace Abc\Bundle\WorkflowBundle\Tests\Controller;

use Abc\Bundle\WorkflowBundle\Model\TaskManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\TaskTypeManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExecutionControllerTest extends WebTestCase
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

        /** @var TaskTypeManagerInterface $taskTypeManager */
        $taskTypeManager = static::$kernel->getContainer()->get('abc.workflow.task_type_manager');

        /** @var WorkflowManagerInterface $workflowManager */
        $workflowManager = static::$kernel->getContainer()->get('abc.workflow.workflow_manager');

        /** @var TaskManagerInterface $taskManager */
        $taskManager = static::$kernel->getContainer()->get('abc.workflow.task_manager');

        $workflow = $workflowManager->create();
        $workflow->setName('Test Workflow');
        $workflow->setDisabled(false);
        $workflowManager->update($workflow);

        $type = $taskTypeManager->create();
        $type->setName('Test Type');
        $type->setJobType('test');
        $taskTypeManager->update($type);

        $task = $taskManager->create();
        $task->setWorkflow($workflow);
        $task->setDisabled(false);
        $task->setPosition(1);
        $task->setType($type);
        $taskManager->update($task);
    }

    public function testExecute()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/workflow/');

        $editLink = $crawler->selectLink('Show')->link();

        $crawler = $client->click($editLink);

        $configureLink = $crawler->selectLink('Execute')->link();

        $client->click($configureLink);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testHistory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/workflow-execution/1/history');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testExecution()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/workflow-execution/1/execution');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testExecutionStatus()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/workflow-execution/1/execution-status');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCancel()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/workflow-execution/1/cancel');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
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