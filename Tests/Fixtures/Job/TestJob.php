<?php

namespace Abc\Bundle\WorkflowBundle\Tests\Fixtures\Job;

use Abc\Bundle\JobBundle\Job\Executable;
use Abc\Bundle\JobBundle\Job\Job;

class TestJob implements Executable
{
    /**
     * @param Job $job
     * @return void
     */
    public function execute(Job $job)
    {

    }
} 