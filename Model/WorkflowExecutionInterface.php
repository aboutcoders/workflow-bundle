<?php
namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\JobBundle\Model\JobInterface;

interface WorkflowExecutionInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return JobInterface
     */
    public function getJob();

    /**
     * @return string
     */
    public function getTicket();

    /**
     * @param string $ticket
     */
    public function setTicket($ticket);

    /**
     * @param JobInterface $job
     */
    public function setJob(JobInterface $job);

    /**
     * @return WorkflowInterface
     */
    public function getWorkflow();

    /**
     * @param WorkflowInterface $workflow
     */
    public function setWorkflow(WorkflowInterface $workflow);
} 