<?php

namespace Zu\HealthCheckBundle\Objects;

use Zu\HealthCheckBundle\Enum\CheckStatusEnum;

class Data
{
    private string $name;
    private ?CheckStatusEnum $status = null;
    private string $message;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): ?CheckStatusEnum
    {
        return $this->status;
    }

    public function setStatus(CheckStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
