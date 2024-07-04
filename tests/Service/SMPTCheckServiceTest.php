<?php

namespace Zu\HealthCheckBundleTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Exception\SMTPCheckerException;
use Zu\HealthCheckBundle\Objects\Data;
use Zu\HealthCheckBundle\Service\SMPTCheckService;

class SMPTCheckServiceTest extends KernelTestCase
{
    private $containerMock;
    private $mailerMock;

    protected function setUp(): void
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->mailerMock = $this->createMock(MailerInterface::class);
    }

    public function testCheckSuccess(): void
    {
        $this->containerMock->method('get')
            ->with('mailer.mailer')
            ->willReturn($this->mailerMock);

        $service = new SMPTCheckService($this->containerMock);

        $this->mailerMock->expects($this->once())
            ->method('send')
            ->with($this->anything());

        $response = $service->check();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('"status":"%s"', CheckStatusEnum::CONNECTION_OK->value
            ), $response->getContent()
        );
    }

    public function testCheckFailure(): void
    {
        $this->containerMock->method('get')
            ->with('mailer.mailer')
            ->willReturn($this->mailerMock);

        $service = new SMPTCheckService($this->containerMock);

        $this->mailerMock->expects($this->once())
            ->method('send')
            ->willThrowException(new \Exception());

        $response = $service->check();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('"status":"%s"', CheckStatusEnum::CONNECTION_ERROR->value
            ), $response->getContent()
        );
    }

    public function testGetServices(): void
    {
        $this->containerMock->method('get')
            ->with('mailer.mailer')
            ->willReturn($this->mailerMock);

        $service = new SMPTCheckService($this->containerMock);
        $reflectionClass = new \ReflectionClass(SMPTCheckService::class);

        $this->assertTrue($reflectionClass->hasProperty('mailer'), 'Property mailer does not exist');

        $property = $reflectionClass->getProperty('mailer');
        $property->setAccessible(true);

        $entityManager = $property->getValue($service);

        $this->assertInstanceOf(MailerInterface::class, $entityManager, 'The mailer property is not an instance of MailerInterface');
    }

    public function testGetServicesCanNotGetEntityManagerGetsNullInstead(): void
    {
        $this->containerMock->method('get')
            ->with('mailer.mailer')
            ->willReturn(null);

        $this->expectException(SMTPCheckerException::class);
        $this->expectExceptionMessage('Mailer not found in container. Have you installed or enabled Mailer?');

        $reflectionMethod = new \ReflectionMethod(SMPTCheckService::class, 'getService');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke(new SMPTCheckService($this->containerMock));
    }

    public function testGetServicesCanNotGetEntityManagerGetsOtherObjectInstead(): void
    {
        $this->containerMock->method('get')
            ->with('mailer.mailer')
            ->willReturn(new Data('boo'));

        $this->expectException(SMTPCheckerException::class);
        $this->expectExceptionMessage('Mailer service is not an instance of Mailer.');

        $reflectionMethod = new \ReflectionMethod(SMPTCheckService::class, 'getService');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke(new SMPTCheckService($this->containerMock));
    }
}
