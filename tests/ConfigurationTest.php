<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Yaml;
use Zu\HealthCheckBundle\ZuHealthCheckBundle;

class ConfigurationTest extends TestCase
{
    private $configuration;
    private array $configs;

    protected function setupConfig(string $yaml = ''): void
    {
        $this->configuration = (new ZuHealthCheckBundle())
            ->getContainerExtension()
            ->getConfiguration([], new ContainerBuilder(new ParameterBag()));

        $this->configs = [Yaml::parse($yaml)];
    }

    private function getConfig(): array
    {
        return (new Processor())->processConfiguration($this->configuration, $this->configs);
    }

    public function testEmptyConfig(): void
    {
        self::setupConfig();
        $result = self::getConfig();

        self::assertSame(['type' => ['smtp' => false, 'doctrine' => false]], $result);
    }

    public function testAllCheckConfig(): void
    {
        self::setupConfig('type: { smtp: true, doctrine: true }');
        $result = self::getConfig();

        self::assertSame(['type' => ['smtp' => true, 'doctrine' => true]], $result);
    }

    public function testLoadExtensionWithAllChecksEnabled(): void
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $containerConfigurator = $this->createMock(ContainerConfigurator::class);

        self::setupConfig('type: { smtp: true, doctrine: true }');
        $config = self::getConfig();

        $bundle = new ZuHealthCheckBundle();
        $bundle->loadExtension($config, $containerConfigurator, $containerBuilder);

        // Assert that the container has the expected services and aliases
        // Adjust these assertions based on the actual services and aliases your bundle registers
        $this->assertTrue($containerBuilder->hasDefinition('zu_health_check.service.smtp_check'));
        $this->assertTrue($containerBuilder->hasDefinition('zu_health_check.service.doctrine_check'));
    }

    public function testLoadExtensionWithNoChecksEnabled(): void
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $containerConfigurator = $this->createMock(ContainerConfigurator::class);

        self::setupConfig();
        $config = self::getConfig();

        $bundle = new ZuHealthCheckBundle();
        $bundle->loadExtension($config, $containerConfigurator, $containerBuilder);

        // Assert that the container has the expected services and aliases
        // Adjust these assertions based on the actual services and aliases your bundle registers
        $this->assertFalse($containerBuilder->hasDefinition('zu_health_check.service.smtp_check'));
        $this->assertFalse($containerBuilder->hasDefinition('zu_health_check.service.doctrine_check'));
    }
}