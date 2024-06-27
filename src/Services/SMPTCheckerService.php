<?php

namespace Zu\HealthCheckBundle\Services;

class SMPTCheckerService implements CheckerInterface
{
    public function check(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        return true;
    }

    public function getName(): string
    {
        return 'SMPT';
    }
}