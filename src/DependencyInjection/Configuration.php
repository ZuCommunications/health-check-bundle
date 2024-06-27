<?php
// src/DependencyInjection/Configuration.php
namespace Zu\HealthCheckBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
//    public function getConfigTreeBuilder(): TreeBuilder
//    {
//        $treeBuilder = new TreeBuilder('zu_health_check');
//
//        $rootNode = $treeBuilder->getRootNode();
//
//        $rootNode
//            ->children()
//            ->arrayNode('types')
//            ->children()
//            ->booleanNode('doctrine')->defaultFalse()->end()
//            ->booleanNode('smtp')->defaultFalse()->end()
//            ->end()
//            ->end()
//            ->end()
//        ;
//
//        return $treeBuilder;
//    }
}
