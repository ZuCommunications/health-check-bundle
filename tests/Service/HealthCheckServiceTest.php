<?php

namespace Zu\HealthCheckBundleTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zu\HealthCheckBundle\Service\AbstractChecker;
use Zu\HealthCheckBundle\Service\HealthCheckService;
use Zu\HealthCheckBundle\Service\DoctrineCheckService;
use Zu\HealthCheckBundle\Service\SMPTCheckService;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthCheckServiceTest extends KernelTestCase
{
    private $doctrineCheckServiceMock;
    private $smtpCheckServiceMock;

    protected function setUp(): void
    {
        $this->doctrineCheckServiceMock = $this->createMock(DoctrineCheckService::class);
        $this->smtpCheckServiceMock = $this->createMock(SMPTCheckService::class);
    }

    public function testCheckAllServicesOk(): void
    {
        $this->doctrineCheckServiceMock->method('check')->willReturn(new JsonResponse(['status' => AbstractChecker::$CONNECTION_OK]));
        $this->smtpCheckServiceMock->method('check')->willReturn(new JsonResponse(['status' => AbstractChecker::$CONNECTION_OK]));

        $service = new HealthCheckService($this->doctrineCheckServiceMock, $this->smtpCheckServiceMock);
        $response = $service->check();

        $expectedResponse = new JsonResponse([['status' => AbstractChecker::$CONNECTION_OK], ['status' => AbstractChecker::$CONNECTION_OK]]);
        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
    }

    public function testCheckOneServiceFails(): void
    {
        $this->doctrineCheckServiceMock->method('check')->willReturn(new JsonResponse(['status' => AbstractChecker::$CONNECTION_OK]));
        $this->smtpCheckServiceMock->method('check')->willReturn(new JsonResponse(['status' => AbstractChecker::$CONNECTION_FAIL]));

        $service = new HealthCheckService($this->doctrineCheckServiceMock, $this->smtpCheckServiceMock);
        $response = $service->check();

        $expectedResponse = new JsonResponse([['status' => AbstractChecker::$CONNECTION_OK], ['status' => AbstractChecker::$CONNECTION_FAIL]]);
        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
    }

    public function testCheckAllServicesFail(): void
    {
        $this->doctrineCheckServiceMock->method('check')->willReturn(new JsonResponse(['status' => AbstractChecker::$CONNECTION_FAIL]));
        $this->smtpCheckServiceMock->method('check')->willReturn(new JsonResponse(['status' => AbstractChecker::$CONNECTION_FAIL]));

        $service = new HealthCheckService($this->doctrineCheckServiceMock, $this->smtpCheckServiceMock);
        $response = $service->check();

        $expectedResponse = new JsonResponse([['status' => AbstractChecker::$CONNECTION_FAIL], ['status' => AbstractChecker::$CONNECTION_FAIL]]);
        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
    }
}