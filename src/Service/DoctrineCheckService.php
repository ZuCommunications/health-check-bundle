<?php

namespace Zu\HealthCheckBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;

class DoctrineCheckService extends AbstractChecker implements CheckerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function check(): JsonResponse
    {
        try {
            $this->entityManager->getConnection()->connect();
            if ($this->entityManager->getConnection()->isConnected()) {
                $this->data->setStatus(CheckStatusEnum::CONNECTION_OK);
            } else {
                $this->data->setStatus(CheckStatusEnum::CONNECTION_FAIL);
                $this->data->setMessage(AbstractChecker::$CONNECTION_FAILED_MESSAGE);
            }
        } catch (\Exception $e) {
            $this->data->setStatus(CheckStatusEnum::CONNECTION_ERROR);
            $this->data->setMessage(AbstractChecker::$CONNECTION_ERROR_MESSAGE);
        }

        return $this->createResponse();
    }

    public function getName(): string
    {
        return 'doctrine';
    }
}
