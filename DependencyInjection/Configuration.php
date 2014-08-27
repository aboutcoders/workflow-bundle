<?php

namespace Abc\Bundle\WorkflowBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('abc_workflow');

        $supportedDrivers = array('orm', 'custom');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of ' . json_encode($supportedDrivers))
                    ->end()
                    ->cannotBeOverwritten()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
                ->scalarNode('filesystem')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();

        $this->addWorkflowSection($rootNode);
        $this->addTaskSection($rootNode);

        return $treeBuilder;
    }

    private function addWorkflowSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('workflow')
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
            ->scalarNode('workflow_manager')->defaultValue('abc.workflow.workflow_manager.default')->end()
            ->end()
            ->children()
            ->scalarNode('execution_manager')->defaultValue('abc.workflow.execution_manager.default')->end()
            ->end()
            ->end()
            ->end();
    }

    private function addTaskSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('task')
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
            ->scalarNode('task_manager')->defaultValue('abc.workflow.task_manager.default')->end()
            ->end()
            ->children()
            ->scalarNode('task_type_manager')->defaultValue('abc.workflow.task_type_manager.default')->end()
            ->end()
            ->children()
            ->scalarNode('category_manager')->defaultValue('abc.workflow.category_manager.default')->end()
            ->end()
            ->end()
            ->end();
    }

}
