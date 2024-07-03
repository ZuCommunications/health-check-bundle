<?php

namespace Zu\HealthCheckBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;

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
                $this->data->setStatus(CheckStatusEnum::CONNECTION_OK);
            } else {
                $this->data->setStatus(CheckStatusEnum::CONNECTION_FAIL);
                $this->data->setMessage(AbstractChecker::$CONNECTION_FAILED_MESSAGE);
            }
        } catch(\Exception $e) {
            $this->data->setStatus(CheckStatusEnum::CONNECTION_ERROR);
            $this->data->setMessage(AbstractChecker::$CONNECTION_ERROR_MESSAGE);
        }
        return $this->createResponse();
    }

    function getName(): string
    {
        return 'doctrine';
    }
}