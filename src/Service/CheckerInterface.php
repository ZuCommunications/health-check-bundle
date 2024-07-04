<?php

namespace Zu\HealthCheckBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

interface CheckerInterface
{
    public function check(): JsonResponse;

    public function createResponse(): JsonResponse;

    public function getName(): string;
}
