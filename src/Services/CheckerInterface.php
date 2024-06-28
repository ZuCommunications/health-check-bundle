<?php

namespace Zu\HealthCheckBundle\Services;

use Symfony\Component\HttpFoundation\JsonResponse;

interface CheckerInterface
{
    public function check(): JsonResponse;
    function createResponse(): JsonResponse;
    function getName(): string;
}