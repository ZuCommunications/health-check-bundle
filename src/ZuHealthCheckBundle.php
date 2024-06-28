<?php

namespace Zu\HealthCheckBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Zu\HealthCheckBundle\Controller\HealthCheckController;
use Zu\HealthCheckBundle\Services\DoctrineCheckService;
use Zu\HealthCheckBundle\Services\HealthCheckService;

class ZuHealthCheckBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()->children()
            ->arrayNode('type')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('smtp')->defaultTrue()->end()
            ->booleanNode('doctrine')->defaultTrue()->end()
            ->end()
            ->end()
            ->end();

    }

    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $builder): void
    {
        $idPrefix = 'zu_health_check.';
        $servicePrefix = 'service.';
        $controllerPrefix = 'controller.';

        // Controllers
        $healthCheckControllerDef = new Definition(HealthCheckController::class);
        $healthCheckControllerDef->setPublic(true);
        $healthCheckControllerDef->setArguments([
            new Reference($idPrefix . $servicePrefix . 'health_check')
        ]);
        $healthCheckControllerDef->addMethodCall('setContainer', [new Reference('service_container')]);
        $builder->setDefinition($idPrefix . $controllerPrefix . 'health_check', $healthCheckControllerDef);
        $builder->setAlias(HealthCheckController::class, $idPrefix . $controllerPrefix . 'health_check')->setPublic(true);

        // Services
        $healthCheckServiceDef = new Definition(HealthCheckService::class);
        $healthCheckServiceDef->setArguments([
            new Reference($idPrefix . $servicePrefix . 'doctrine_check')
        ]);
        $builder->setDefinition($idPrefix . $servicePrefix . 'health_check', $healthCheckServiceDef);
        $builder->setAlias(HealthCheckService::class, $idPrefix . $servicePrefix . 'health_check');

        $doctrineCheckServiceDef = new Definition(DoctrineCheckService::class);
        $doctrineCheckServiceDef->setArgument('$enabled', $config['type']['doctrine']);
        $builder->setDefinition($idPrefix . $servicePrefix . 'doctrine_check', $doctrineCheckServiceDef);
        $builder->setAlias(DoctrineCheckService::class, $idPrefix . $servicePrefix . 'doctrine_check');
    }
}