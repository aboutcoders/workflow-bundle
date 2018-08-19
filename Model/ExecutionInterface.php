<?php
namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\JobBundle\Job\Status;

interface ExecutionInterface
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
     * @return string
     */
    public function getTicket();

    /**
     * @param string $ticket
     */
    public function setTicket($ticket);

    /**
     * @return WorkflowInterface
     */
    public function getWorkflow();

    /**
     * @param WorkflowInterface $workflow
     */
    public function setWorkflow(WorkflowInterface $workflow);

    /**
     * @param Status $status
     * @return void
     */
    public function setStatus(Status $status);

    /**
     * @return Status
     */
    public function getStatus();

    /**
     * @param double $status The execution time in microseconds
     * @return void
     */
    public function setExecutionTime($status);

    /**
     * @return double Execution time in microseconds
     */
    public function getExecutionTime();

    /**
     * @param integer $executionNumber
     * @return void
     */
    public function setExecutionNumber($executionNumber);

    /**
     * @return integer
     */
    public function getExecutionNumber();

    /**
     * @param array $context
     * @return void
     */
    public function setContext($context);

    /**
     * @return array
     */
    public function getContext();

}