<?php

namespace Zu\HealthCheckBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Zu\HealthCheckBundle\Service\HealthCheckService;

class HealthCheckController extends AbstractController
{
    public function __construct(
        private HealthCheckService $healthCheckService
    ) {
    }

    #[Route('/ping', name: 'zu_health_check_ping')]
    public function ping(): Response
    {
        return new Response('pong');
    }

    /**
     * @throws \Exception
     */
    #[Route('/health-check', name: 'zu_health_check_health-check')]
    public function healthCheck(): Response
    {
        return $this->healthCheckService->check();
    }
}
