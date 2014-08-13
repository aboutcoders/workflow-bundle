<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Sonata\NotificationBundle\SonataNotificationBundle(),
            new \Abc\Bundle\FileDistributionBundle\AbcFileDistributionBundle(),
            new \Abc\Bundle\ProcessControlBundle\AbcProcessControlBundle(),
            new \Abc\Bundle\SchedulerBundle\AbcSchedulerBundle(),
            new \Abc\Bundle\JobBundle\AbcJobBundle(),
            new \Abc\Bundle\WorkflowBundle\AbcWorkflowBundle(),
            new \Abc\Bundle\SequenceBundle\AbcSequenceBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->rootDir .'/../../../../build/app/cache';
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return $this->rootDir .'/../../../../build/app/logs';
    }
}
