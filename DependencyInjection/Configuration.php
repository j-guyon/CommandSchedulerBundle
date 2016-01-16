<?php

namespace JMose\CommandSchedulerBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('jmose_command_scheduler');
        $rootNode
            ->children()
                ->scalarNode('doctrine_manager')->defaultValue('default')->end()
                ->scalarNode('log_path')->defaultValue('app\logs')->end()
                ->scalarNode('lock_timeout')->defaultValue(false)->end()
                ->arrayNode('monitor_mail')
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()
                ->booleanNode('send_ok')->defaultValue(false)->end()
                ->variableNode('excluded_command_namespaces')
                    ->defaultValue(array(
                        '_global',
                        'scheduler',
                        'server',
                        'container',
                        'config',
                        'generate',
                        'init',
                        'router',
                    ))
                    ->validate()
                        ->always(function($value) {
                            if (is_string($value)) {
                                return explode(',', $value);
                            }
                            return $value;
                        })
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
