<?php

namespace Zu\HealthCheckBundle\Services;

class HealthCheckService
{
    public function __construct(
        private DoctrineCheckService $doctrineCheckService,
    )
    {
    }

    public function check()
    {
        return $this->doctrineCheckService->check();
    }
}