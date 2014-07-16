<?php
namespace Abc\Bundle\WorkflowBundle\Model;

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
} 