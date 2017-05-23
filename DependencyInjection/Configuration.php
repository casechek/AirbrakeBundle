<?php
namespace Incompass\AirbrakeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Incompass\AirbrakeBundle\DependencyInjection
 * @author  Joe Mizzi <joe@casechek.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('airbrake');
        $rootNode
            ->children()
            ->scalarNode('project_id')
            ->isRequired()
            ->end()
            ->scalarNode('project_key')
            ->isRequired()
            ->end()
            ->scalarNode('host')
            ->defaultValue('api.airbrake.io')
            ->end()
            ->arrayNode('ignored_exceptions')
            ->prototype('scalar')->end()
            ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
