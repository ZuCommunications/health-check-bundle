<?php

namespace Zu\HealthCheckBundle\Services;

use Symfony\Component\HttpFoundation\JsonResponse;

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
        $combined = [];

        foreach ($responses as $response) {
            $body = $response->getContent();
            $data = json_decode($body, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $combined[] = $data;
            }
        }

        return new JsonResponse($combined);
    }
}