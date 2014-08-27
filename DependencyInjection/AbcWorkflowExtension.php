<?php

namespace Abc\Bundle\WorkflowBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AbcWorkflowExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/services'));

        if ('custom' !== $config['db_driver']) {
            $loader->load(sprintf('%s.xml', $config['db_driver']));
        }


        $container->setAlias('abc.workflow.filesystem', 'abc.file_distribution.filesystem.' . $config['filesystem']);

        $loader->load('services.xml');

        $this->remapParametersNamespaces($config, $container, array(
                '' => array(
                    'model_manager_name' => 'abc.workflow.model_manager_name'
                )
            )
        );

        if (!empty($config['workflow'])) {
            $this->loadWorkflow($config['workflow'], $container, $loader, $config['db_driver']);
        }

        if (!empty($config['task'])) {
            $this->loadTask($config['task'], $container, $loader, $config['db_driver']);
        }

    }

    private function loadWorkflow(array $config, ContainerBuilder $container, XmlFileLoader $loader, $dbDriver)
    {
        if ('custom' !== $dbDriver) {
            $loader->load(sprintf('%s_workflow.xml', $dbDriver));
        }

        $container->setAlias('abc.workflow.workflow_manager', $config['workflow_manager']);
        $container->setAlias('abc.workflow.execution_manager', $config['execution_manager']);

        $this->remapParametersNamespaces(
            $config,
            $container,
            array(
                '' => array(
                    'workflow_class'  => 'abc.workflow.model.workflow.class',
                    'execution_class' => 'abc.workflow.model.execution.class',
                )
            )
        );
    }

    private function loadTask(array $config, ContainerBuilder $container, XmlFileLoader $loader, $dbDriver)
    {
        if ('custom' !== $dbDriver) {
            $loader->load(sprintf('%s_task.xml', $dbDriver));
            $loader->load(sprintf('%s_task_type.xml', $dbDriver));
        }

        $container->setAlias('abc.workflow.task_manager', $config['task_manager']);
        $container->setAlias('abc.workflow.task_type_manager', $config['task_type_manager']);
        $container->setAlias('abc.workflow.category_manager', $config['category_manager']);

        $this->remapParametersNamespaces(
            $config,
            $container,
            array(
                '' => array(
                    'task_class'               => 'abc.workflow.model.task.class',
                    'task_type_class'          => 'abc.workflow.model.task_type.class',
                    'task_type_category_class' => 'abc.workflow.model.task_type_category.class',
                )
            )
        );
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }
}
