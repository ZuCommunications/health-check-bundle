<?php

namespace Zu\HealthCheckBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Zu\HealthCheckBundle\Controller\HealthCheckController;
use Zu\HealthCheckBundle\Service\DoctrineCheckService;
use Zu\HealthCheckBundle\Service\HealthCheckService;
use Zu\HealthCheckBundle\Service\SMPTCheckService;

class ZuHealthCheckBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()->children()
            ->arrayNode('type')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('smtp')->defaultFalse()->end()
            ->booleanNode('doctrine')->defaultFalse()->end()
            ->end()
            ->end()
            ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $builder): void
    {
        // String Helpers
        $idPrefix = 'zu_health_check.';
        $servicePrefix = 'service.';
        $controllerPrefix = 'controller.';

        // Configs
        $doctrineCheckEnabled = $config['type']['doctrine'];
        $smtpCheckEnabled = $config['type']['smtp'];

        // Service Definitions (Aliases)
        $healthCheckerServiceAlias = $idPrefix . $servicePrefix . 'health_check';
        $doctrineCheckerServiceAlias = $idPrefix . $servicePrefix . 'doctrine_check';
        $smtpCheckerServiceAlias = $idPrefix . $servicePrefix . 'smtp_check';
        $healthCheckerControllerAlias = $idPrefix . $controllerPrefix . 'health_check';

        // Controllers
        $healthCheckControllerDef = new Definition(HealthCheckController::class);
        $healthCheckControllerDef->setPublic(true);
        $healthCheckControllerDef->setArguments([
            new Reference($healthCheckerServiceAlias)
        ]);
        $healthCheckControllerDef->addMethodCall('setContainer', [new Reference('service_container')]);
        $builder->setDefinition($healthCheckerControllerAlias, $healthCheckControllerDef);
        $builder->setAlias(HealthCheckController::class, $healthCheckerControllerAlias)->setPublic(true);

        // Services
        $healthCheckServiceDef = new Definition(HealthCheckService::class);
        // Only pass in the services if they are enabled. Otherwise, pass in null. Handle this in the service itself.
        $healthCheckServiceDef->setArgument('$doctrineCheckService', $doctrineCheckEnabled === true ? new Reference($doctrineCheckerServiceAlias) : null);
        $healthCheckServiceDef->setArgument('$smtpCheckService', $smtpCheckEnabled === true ? new Reference($smtpCheckerServiceAlias) : null);
        $builder->setDefinition($healthCheckerServiceAlias, $healthCheckServiceDef);
        $builder->setAlias(HealthCheckService::class, $healthCheckerServiceAlias);

        // Only register services if enabled.
        if ($doctrineCheckEnabled === true) {
            $doctrineCheckServiceDef = new Definition(DoctrineCheckService::class);
            $builder->setDefinition($doctrineCheckerServiceAlias, $doctrineCheckServiceDef);
            $builder->setAlias(DoctrineCheckService::class, $doctrineCheckerServiceAlias);
            $doctrineCheckServiceDef->setArgument('$container', new Reference('service_container'));
        }

        if ($smtpCheckEnabled === true) {
            $smtpCheckServiceDef = new Definition(SMPTCheckService::class);
            $builder->setDefinition($smtpCheckerServiceAlias, $smtpCheckServiceDef);
            $builder->setAlias(SMPTCheckService::class, $smtpCheckerServiceAlias);
            $smtpCheckServiceDef->setArgument('$mailer', new Reference('mailer.mailer'));
        }
    }
}