<?php

namespace Zu\HealthCheckBundleTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Zu\HealthCheckBundle\Service\AbstractChecker;
use Zu\HealthCheckBundle\Service\SMPTCheckService;
use Symfony\Component\HttpFoundation\JsonResponse;

class SMPTCheckServiceTest extends KernelTestCase
{
    private $mailerMock;

    protected function setUp(): void
    {
        self::bootKernel();
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
            sprintf('"status":"%s"', AbstractChecker::$CONNECTION_OK
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
            sprintf('"status":"%s"', AbstractChecker::$CONNECTION_ERROR
            ), $response->getContent()
        );
    }
}