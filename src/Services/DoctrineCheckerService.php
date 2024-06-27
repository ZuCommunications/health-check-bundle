<?php

namespace Zu\HealthCheckBundle\Services;

class DoctrineCheckerService implements CheckerInterface
{
    public function __construct(
        private bool $enabled
    )
    {
    }

    public function check(): bool
    {
        if (!$this->enabled) {
            return false;
        }
        return true;
    }

    public function getName(): string
    {
        return 'doctrine';
    }
}