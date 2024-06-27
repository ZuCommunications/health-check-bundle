<?php

namespace Zu\HealthCheckBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Zu\HealthCheckBundle\Services\DoctrineCheckService;

class ZuHealthCheckBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('types')
                    ->children()
                        ->booleanNode('doctrine')->defaultFalse()->end()
                        ->booleanNode('smtp')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        // Set the parameter for the DoctrineCheckService
        $container->parameters()
            ->set('zu_health_check.types.doctrine', $config['types']['doctrine']);

        // Ensure the DoctrineCheckService is correctly registered with the configuration parameter
        $container->services()
            ->get('zu_health_check.service.doctrine_check')
            ->arg('$enabled', '%zu_health_check.types.doctrine%');
    }
}