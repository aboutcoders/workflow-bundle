<?php

namespace Abc\Bundle\WorkflowBundle\Workflow;

use Abc\Bundle\JobBundle\Job as Job;
use Abc\Bundle\SchedulerBundle\Model\ScheduleInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionInterface;
use Abc\Bundle\WorkflowBundle\Model\ExecutionManagerInterface;
use Abc\Bundle\WorkflowBundle\Model\WorkflowManagerInterface;
use Abc\Bundle\WorkflowBundle\Workflow\Exception\WorkflowNotFoundException;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class Manager implements ManagerInterface
{
    /** @var WorkflowManagerInterface */
    protected $workflowManager;
    /** @var ExecutionManagerInterface */
    protected $executionManager;
    /** @var Job\ManagerInterface */
    protected $jobManager;

    /**
     * @param Job\ManagerInterface      $jobManager
     * @param WorkflowManagerInterface  $workflowManager
     * @param ExecutionManagerInterface $executionManager
     */
    function __construct(Job\ManagerInterface $jobManager, WorkflowManagerInterface $workflowManager, ExecutionManagerInterface $executionManager)
    {
        $this->jobManager       = $jobManager;
        $this->workflowManager  = $workflowManager;
        $this->executionManager = $executionManager;
    }

    /**
     * @param int               $id
     * @param \Serializable     $parameters
     * @param ScheduleInterface $schedule
     * @param mixed|null        $response
     * @return ExecutionInterface
     * @throws WorkflowNotFoundException If a workflow with the given id does not exist
     */
    public function execute($id, \Serializable $parameters = null, ScheduleInterface $schedule = null, $response = null)
    {
        $workflow = $this->workflowManager->findById($id);

        if($workflow == null)
        {
            throw new WorkflowNotFoundException($id);
        }

        if($response == null)
        {
            $response = new Response();
        }

        $configuration = new Configuration($id, $parameters, $workflow->getCreateDirectory(), $workflow->getRemoveDirectory());

        $ticket = $this->jobManager->addJob('workflow', $configuration, $schedule, $response);

        $execution = $this->executionManager->create($ticket, $workflow);
        $this->executionManager->update($execution);

        return $execution;
    }
}