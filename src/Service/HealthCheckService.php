<?php

namespace Zu\HealthCheckBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Exception\JsonResponseException;

class HealthCheckService
{
    public function __construct(
        private ?DoctrineCheckService $doctrineCheckService,
        private ?SMPTCheckService $smtpCheckService
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function check(): JsonResponse
    {
        $responses = [];

        if ($this->doctrineCheckService !== null) {
            $responses[] = $this->doctrineCheckService->check();
        }
        if ($this->smtpCheckService !== null) {
            $responses[] = $this->smtpCheckService->check();
        }

        return $this->combineJsonResponses($responses);
    }

    private function combineJsonResponses(array $responses): JsonResponse
    {
        $hasError = false;
        $combined = [];

        foreach ($responses as $response) {
            $body = $response->getContent();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new JsonResponseException(500, 'Unable to decode JSON response. Error: ' . json_last_error_msg());
            }

            if ($this->hasCheckedFailed(CheckStatusEnum::from($data['status'])) === true){
                $hasError = true;
            }
            $combined[] = $data;
        }

        return new JsonResponse($combined, $hasError ? 500 : 200);
    }

    public function hasCheckedFailed(CheckStatusEnum $status): bool
    {
        return match ($status) {
            CheckStatusEnum::CONNECTION_OK => false,
            default => true,
        };
    }
}