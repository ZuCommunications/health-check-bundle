<?php

namespace Zu\HealthCheckBundle\Services;

interface CheckerInterface
{
    public function check(): bool;
    public function getName(): string;
}