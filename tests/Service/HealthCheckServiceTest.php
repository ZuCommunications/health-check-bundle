<?php

namespace Zu\HealthCheckBundleTests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Exception\JsonResponseException;
use Zu\HealthCheckBundle\Objects\Data;
use Zu\HealthCheckBundle\Service\DoctrineCheckService;
use Zu\HealthCheckBundle\Service\HealthCheckService;
use Zu\HealthCheckBundle\Service\SMPTCheckService;
use Zu\HealthCheckBundle\Utils\SerializerHelper;

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
        $this->doctrineCheckServiceMock->method('check')->willReturn(new JsonResponse(['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_OK]));
        $this->smtpCheckServiceMock->method('check')->willReturn(new JsonResponse(['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_OK]));

        $service = new HealthCheckService($this->doctrineCheckServiceMock, $this->smtpCheckServiceMock);
        $response = $service->check();

        $expectedResponse = new JsonResponse([['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_OK], ['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_OK]]);
        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
    }

    public function testCheckOneServiceFails(): void
    {
        $this->doctrineCheckServiceMock->method('check')->willReturn(new JsonResponse(['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_OK]));
        $this->smtpCheckServiceMock->method('check')->willReturn(new JsonResponse(['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_FAIL]));

        $service = new HealthCheckService($this->doctrineCheckServiceMock, $this->smtpCheckServiceMock);
        $response = $service->check();

        $expectedResponse = new JsonResponse([['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_OK], ['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_FAIL]]);
        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
    }

    public function testCheckAllServicesFail(): void
    {
        $this->doctrineCheckServiceMock->method('check')->willReturn(new JsonResponse(['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_FAIL]));
        $this->smtpCheckServiceMock->method('check')->willReturn(new JsonResponse(['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_FAIL]));

        $service = new HealthCheckService($this->doctrineCheckServiceMock, $this->smtpCheckServiceMock);
        $response = $service->check();

        $expectedResponse = new JsonResponse([['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_FAIL], ['name' => 'boo', 'status' => CheckStatusEnum::CONNECTION_FAIL]]);
        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testHasCheckedFailedWithOkStatus(): void
    {
        $healthCheckService = new HealthCheckService(null, null);

        $reflectionMethod = new \ReflectionMethod(HealthCheckService::class, 'hasCheckedFailed');
        $reflectionMethod->setAccessible(true);

        $statusOk = CheckStatusEnum::CONNECTION_OK;
        $result = $reflectionMethod->invokeArgs($healthCheckService, [$statusOk]);

        $this->assertFalse($result, 'The method hasCheckedFailed should return false for CONNECTION_OK status.');
    }

    public function testHasCheckedFailedWithNoneOkStatus(): void
    {
        $healthCheckService = new HealthCheckService(null, null);

        $reflectionMethod = new \ReflectionMethod(HealthCheckService::class, 'hasCheckedFailed');
        $reflectionMethod->setAccessible(true);

        $statusOk = CheckStatusEnum::CONNECTION_FAIL;
        $result = $reflectionMethod->invokeArgs($healthCheckService, [$statusOk]);

        $this->assertTrue($result, 'The method hasCheckedFailed should return true for None CONNECTION_OK status.');
    }

    public function testExtractDataFromResponse(): void
    {
        $healthCheckService = new HealthCheckService(null, null);

        $reflectionMethod = new \ReflectionMethod(HealthCheckService::class, 'extractDataFromResponse');
        $reflectionMethod->setAccessible(true);

        $data = new Data('boo');
        $data->setStatus(CheckStatusEnum::CONNECTION_OK);

        $jsonResponse = JsonResponse::fromJsonString(SerializerHelper::createSerializer()->serialize($data, 'json'));

        $result = $reflectionMethod->invokeArgs($healthCheckService, [$jsonResponse]);

        $this->assertEquals($data, $result);
    }

    public function testExtractDataFromResponseBodyIsNotString(): void
    {
        $healthCheckService = new HealthCheckService(null, null);

        // Create a mock of JsonResponse
        $jsonResponseMock = $this->createMock(JsonResponse::class);
        // Mock getContent() to return invalid JSON
        $jsonResponseMock->method('getContent')->willReturn(false);

        $reflectionMethod = new \ReflectionMethod(HealthCheckService::class, 'extractDataFromResponse');
        $reflectionMethod->setAccessible(true);

        // Expect an exception due to invalid JSON
        $this->expectException(JsonResponseException::class);

        // Invoke the method with the mocked JsonResponse
        $reflectionMethod->invokeArgs($healthCheckService, [$jsonResponseMock]);
    }

    public function testCombineJsonResponsesWithHasCheckedFailed(): void
    {
        $reflectionMethod = new \ReflectionMethod(HealthCheckService::class, 'combineJsonResponses');
        $reflectionMethod->setAccessible(true);

        $data = new Data('boo');
        $data->setStatus(CheckStatusEnum::CONNECTION_OK);
        $jsonResponse1 = JsonResponse::fromJsonString(SerializerHelper::createSerializer()->serialize($data, 'json'));
        $data->setStatus(CheckStatusEnum::CONNECTION_FAIL);
        $jsonResponse2 = JsonResponse::fromJsonString(SerializerHelper::createSerializer()->serialize($data, 'json'));

        $responses = [$jsonResponse1, $jsonResponse2];

        $response = $reflectionMethod->invokeArgs(new HealthCheckService(null, null), [$responses]);

        $this->assertEquals(500, $response->getStatusCode());
    }
}
