<?php

namespace Zu\HealthCheckBundleTests\Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Exception\DoctrineCheckerException;
use Zu\HealthCheckBundle\Objects\Data;
use Zu\HealthCheckBundle\Service\DoctrineCheckService;

class DoctrineCheckServiceTest extends KernelTestCase
{
    private $containerMock;
    private $entityManagerMock;
    private $connectionMock;

    protected function setUp(): void
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->connectionMock = $this->createMock(Connection::class);

        $this->entityManagerMock->method('getConnection')
            ->willReturn($this->connectionMock);
    }

    public function testCheckConnectionOk(): void
    {
        $this->connectionMock->method('connect')->willReturn(true);
        $this->connectionMock->method('isConnected')->willReturn(true);
        $this->containerMock->method('get')
            ->with('doctrine.orm.entity_manager')
            ->willReturn($this->entityManagerMock);

        $service = new DoctrineCheckService($this->containerMock);
        $response = $service->check();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('"status":"%s"', CheckStatusEnum::CONNECTION_OK->value
            ), $response->getContent()
        );
    }

    public function testCheckConnectionFail(): void
    {
        $this->connectionMock->method('connect')->willReturn(false);
        $this->connectionMock->method('isConnected')->willReturn(false);
        $this->containerMock->method('get')
            ->with('doctrine.orm.entity_manager')
            ->willReturn($this->entityManagerMock);

        $service = new DoctrineCheckService($this->containerMock);
        $response = $service->check();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('"status":"%s"', CheckStatusEnum::CONNECTION_FAIL->value
            ), $response->getContent()
        );
    }

    public function testCheckConnectionError(): void
    {
        $this->connectionMock->method('connect')->willThrowException(new \Exception());
        $this->containerMock->method('get')
            ->with('doctrine.orm.entity_manager')
            ->willReturn($this->entityManagerMock);

        $service = new DoctrineCheckService($this->containerMock);
        $response = $service->check();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('"status":"%s"', CheckStatusEnum::CONNECTION_ERROR->value
            ), $response->getContent()
        );
    }

    public function testGetServices(): void
    {
        $this->containerMock->method('get')
            ->with('doctrine.orm.entity_manager')
            ->willReturn($this->entityManagerMock);

        $service = new DoctrineCheckService($this->containerMock);
        $reflectionClass = new \ReflectionClass(DoctrineCheckService::class);

        $this->assertTrue($reflectionClass->hasProperty('entityManager'), 'Property entityManager does not exist');

        $property = $reflectionClass->getProperty('entityManager');
        $property->setAccessible(true);

        $entityManager = $property->getValue($service);

        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager, 'The entityManager property is not an instance of EntityManagerInterface');
    }

    public function testGetServicesCanNotGetEntityManagerGetsNullInstead(): void
    {
        $this->containerMock->method('get')
            ->willReturn(null);

        $this->expectException(DoctrineCheckerException::class);
        $this->expectExceptionMessage('Doctrine entity manager not found in container. Have you installed or enabled Doctrine?');

        $reflectionMethod = new \ReflectionMethod(DoctrineCheckService::class, 'getService');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke(new DoctrineCheckService($this->containerMock));
    }

    public function testGetServicesCanNotGetEntityManagerGetsOtherObjectInstead(): void
    {
        $this->containerMock->method('get')
            ->willReturn(new Data('boo'));

        $this->expectException(DoctrineCheckerException::class);
        $this->expectExceptionMessage('Doctrine entity manager service is not an instance of EntityManager.');

        $reflectionMethod = new \ReflectionMethod(DoctrineCheckService::class, 'getService');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke(new DoctrineCheckService($this->containerMock));
    }
}
