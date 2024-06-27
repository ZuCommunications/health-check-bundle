<?php

namespace Zu\HealthCheckBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Zu\HealthCheckBundle\Services\HealthCheckService;

class ZuHealthCheckBundle extends AbstractBundle
{
    protected $name = 'ZuHealthCheckBundle';

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
        var_dump($config);
//
//        $container->import('../config/services.yaml');
        $x = $container->services()->get('service_container');

        // Controller


//        $builder
//            ->setDefinition(HealthCheckController::class, new Definition(HealthCheckController::class))
//            ->addMethodCall(
//                'setContainer', [$x]
//            )

//            ->setArgument('$healthCheckService', '@zu_health_check.services.health_check')
        ;
        // Service
//        $builder
//            ->setDefinition('zu_health_check.services.health_check', new Definition(HealthCheckService::class))
//        ;
    }
}