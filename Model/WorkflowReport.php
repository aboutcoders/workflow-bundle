<?php

namespace Abc\Bundle\WorkflowBundle\Model;

use Abc\Bundle\JobBundle\Job\JobInterface;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 *
 */
class WorkflowReport implements WorkflowReportInterface
{
    /**
     * @var JobInterface
     */
    protected $job;

    /**
     * @var array
     */
    protected $logs;

    /**
     * WorkflowReport constructor.
     *
     * @param JobInterface $job
     * @param array        $logs
     */
    public function __construct(JobInterface $job, array $logs)
    {
        $this->job  = $job;
        $this->logs = $logs;
    }

    /**
     * @return JobInterface
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param JobInterface $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param array $logs
     */
    public function setLogs($logs)
    {
        $this->logs = $logs;
    }

}