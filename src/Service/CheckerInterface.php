<?php

namespace Zu\HealthCheckBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

interface CheckerInterface
{
    public function check(): JsonResponse;
    function createResponse(): JsonResponse;
    function getName(): string;
}