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
                        ->booleanNode('smpt')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        // This is not working. Has no effect.
        $container->services()->set('Zu\HealthCheckBundle\Services\DoctrineCheckService')
            ->arg(0, true)
        ;
    }
}