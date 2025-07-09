<?php

namespace Zu\HealthCheckBundleTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Service\SMPTCheckService;

class SMPTCheckServiceTest extends KernelTestCase
{
    private $mailerMock;

    protected function setUp(): void
    {
        $this->mailerMock = $this->createMock(MailerInterface::class);
    }

    public function testCheckSuccess(): void
    {
        $service = new SMPTCheckService($this->mailerMock);

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
        $service = new SMPTCheckService($this->mailerMock);

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

    public function testMailerPropertyIsSet(): void
    {
        $service = new SMPTCheckService($this->mailerMock);
        $reflectionClass = new \ReflectionClass(SMPTCheckService::class);

        $this->assertTrue($reflectionClass->hasProperty('mailer'), 'Property mailer does not exist');

        $property = $reflectionClass->getProperty('mailer');
        $property->setAccessible(true);

        $mailer = $property->getValue($service);

        $this->assertInstanceOf(MailerInterface::class, $mailer, 'The mailer property is not an instance of MailerInterface');
    }
}
