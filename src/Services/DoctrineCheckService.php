<?php

namespace Zu\HealthCheckBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DoctrineCheckService extends AbstractChecker
{

    public function __construct(
        private readonly ContainerInterface $container
    )
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function check(): JsonResponse
    {
        try {
            $entityManager = $this->container->get('doctrine.orm.entity_manager');

            $entityManager->getConnection()->connect();
            if ($entityManager->getConnection()->isConnected()) {
                $this->data->setStatus(AbstractChecker::$CONNECTION_OK);
            } else {
                $this->data->setStatus(AbstractChecker::$CONNECTION_FAIL);
                $this->data->setMessage(AbstractChecker::$CONNECTION_FAILED_MESSAGE);
            }
        } catch(\Exception $e) {
            $this->data->setStatus(AbstractChecker::$CONNECTION_ERROR);
            $this->data->setMessage(AbstractChecker::$CONNECTION_ERROR_MESSAGE);
        }
        return $this->createResponse();
    }

    function getName(): string
    {
        return 'doctrine';
    }
}