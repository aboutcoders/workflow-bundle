<?php

namespace Abc\Bundle\WorkflowBundle\Model;


use Abc\Bundle\JobBundle\Job\JobInterface;

interface WorkflowReportInterface
{
    /**
     * @return JobInterface
     */
    public function getJob();

    /**
     * @param JobInterface $job
     */
    public function setJob($job);

    /**
     * @return array
     */
    public function getLogs();

    /**
     * @param array $logs
     */
    public function setLogs($logs);
}