<?php
namespace Abc\Bundle\WorkflowBundle\Model;

interface TaskInterface
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
     * @return Workflow
     */
    public function getWorkflow();

    /**
     * @param WorkflowInterface $workflow
     */
    public function setWorkflow(WorkflowInterface $workflow);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return int
     */
    public function getClearWorkspaceAfter();

    /**
     * @param int $clearWorkspaceAfter
     */
    public function setClearWorkspaceAfter($clearWorkspaceAfter);

    /**
     * @return TaskType
     */
    public function getType();

    /**
     * @param TaskTypeInterface $type
     * @return void
     */
    public function setType(TaskTypeInterface $type);

    /**
     * @return boolean
     */
    public function isDisabled();

    /**
     * @param boolean $disabled
     */
    public function setDisabled($disabled);

    /**
     * @return boolean
     */
    public function isScheduled();

    /**
     * @param boolean $scheduled
     */
    public function setScheduled($scheduled);

    /**
     * @return \Serializable|null
     */
    public function getParameters();

    /**
     * @param \Serializable|null $parameters
     * @return void
     */
    public function setParameters(\Serializable $parameters = null);

    /**
     * @return ScheduleInterface
     */
    public function getSchedule();

    /**
     * @param ScheduleInterface|null $schedule
     * @return $void
     */
    public function setSchedule(ScheduleInterface $schedule = null);
}