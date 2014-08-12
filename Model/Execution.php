<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\JobBundle\Job\Status;
use Abc\Bundle\JobBundle\Model\JobInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 *
 * @ExclusionPolicy("all")
 */
class Execution implements ExecutionInterface
{
    /**
     * @var int
     * @Expose
     */
    protected $id;

    /**
     * @var \DateTime
     * @SerializedName("created")
     * @Expose
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @SerializedName("lastmodified")
     * @Expose
     */
    protected $updatedAt;

    /**
     * @var WorkflowInterface
     */
    protected $workflow;

    /**
     * @var string
     */
    protected $ticket;

    /**
     * @var Status
     * @Type("string")
     * @Expose
     */
    protected $status;

    /**
     * @var double
     * @Type("double")
     * @Expose
     */
    protected $executionTime;

    function __construct()
    {
        $this->status = Status::REQUESTED();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * {@inheritDoc}
     */
    public function setWorkflow(WorkflowInterface $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * {@inheritDoc}
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * {@inheritDoc}
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function setExecutionTime($executionTime)
    {
        $this->executionTime = $executionTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }
}