<?php

namespace Zu\HealthCheckBundleTests\Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Service\DoctrineCheckService;

class DoctrineCheckServiceTest extends KernelTestCase
{
    private $entityManagerMock;
    private $connectionMock;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->connectionMock = $this->createMock(Connection::class);

        $this->entityManagerMock->method('getConnection')
            ->willReturn($this->connectionMock);
    }

    public function testCheckConnectionOk(): void
    {
        $this->connectionMock->method('connect')->willReturn(true);
        $this->connectionMock->method('isConnected')->willReturn(true);

        $service = new DoctrineCheckService($this->entityManagerMock);
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

        $service = new DoctrineCheckService($this->entityManagerMock);
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

        $service = new DoctrineCheckService($this->entityManagerMock);
        $response = $service->check();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('"status":"%s"', CheckStatusEnum::CONNECTION_ERROR->value
            ), $response->getContent()
        );
    }

    public function testEntityManagerPropertyIsSet(): void
    {
        $service = new DoctrineCheckService($this->entityManagerMock);
        $reflectionClass = new \ReflectionClass(DoctrineCheckService::class);

        $this->assertTrue($reflectionClass->hasProperty('entityManager'), 'Property entityManager does not exist');

        $property = $reflectionClass->getProperty('entityManager');
        $property->setAccessible(true);

        $entityManager = $property->getValue($service);

        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager, 'The entityManager property is not an instance of EntityManagerInterface');
    }
}
