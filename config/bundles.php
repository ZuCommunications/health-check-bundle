<?php

use Zu\HealthCheckBundle\ZuHealthCheckBundle;

return [
    ZuHealthCheckBundle::class => ['all' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
];
