<?php

namespace Zu\HealthCheckBundleTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Service\AbstractChecker;
use Zu\HealthCheckBundle\Service\DoctrineCheckService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;

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

        $this->containerMock->method('get')
            ->with('doctrine.orm.entity_manager')
            ->willReturn($this->entityManagerMock);

        $this->entityManagerMock->method('getConnection')
            ->willReturn($this->connectionMock);
    }

    public function testCheckConnectionOk(): void
    {
        $this->connectionMock->method('connect')->willReturn(true);
        $this->connectionMock->method('isConnected')->willReturn(true);

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

        $service = new DoctrineCheckService($this->containerMock);
        $response = $service->check();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('"status":"%s"', CheckStatusEnum::CONNECTION_ERROR->value
            ), $response->getContent()
        );
    }
}