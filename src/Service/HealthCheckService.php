<?php

namespace Zu\HealthCheckBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Exception\JsonResponseException;
use Zu\HealthCheckBundle\Objects\Data;
use Zu\HealthCheckBundle\Utils\SerializerHelper;

class HealthCheckService
{
    public function __construct(
        private ?DoctrineCheckService $doctrineCheckService,
        private ?SMPTCheckService $smtpCheckService
    ) {
    }

    /**
     * @throws \Exception
     */
    public function check(): JsonResponse
    {
        $responses = [];

        if (null !== $this->doctrineCheckService) {
            $responses[] = $this->doctrineCheckService->check();
        }
        if (null !== $this->smtpCheckService) {
            $responses[] = $this->smtpCheckService->check();
        }

        return $this->combineJsonResponses($responses);
    }

    /**
     * @param array<int, JsonResponse> $responses
     */
    private function combineJsonResponses(array $responses): JsonResponse
    {
        $hasError = false;
        $combined = [];

        foreach ($responses as $response) {
            $deserializerData = $this->extractDataFromResponse($response);

            $status = $deserializerData->getStatus();
            if (!isset($status) || true === $this->hasCheckedFailed($status)) {
                $hasError = true;
            }
            $combined[] = $deserializerData;
        }

        return JsonResponse::fromJsonString(SerializerHelper::createSerializer()->serialize($combined, 'json'), $hasError ? 500 : 200);
    }

    private function hasCheckedFailed(CheckStatusEnum $status): bool
    {
        return match ($status) {
            CheckStatusEnum::CONNECTION_OK => false,
            default => true,
        };
    }

    public function extractDataFromResponse(JsonResponse $response): Data
    {
        $body = $response->getContent();

        if (true !== is_string($body)) {
            throw new JsonResponseException(500, 'Response body is not a string.');
        }

        // deserialize the JSON response
        $serializer = SerializerHelper::createSerializer();

        return $serializer->deserialize($body, Data::class, 'json');
    }
}
