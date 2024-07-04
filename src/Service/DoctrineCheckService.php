<?php

namespace Zu\HealthCheckBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Exception\DoctrineCheckerException;

class DoctrineCheckService extends AbstractChecker implements CheckerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        private readonly ContainerInterface $container
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

    protected function getService(): void
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        if (!isset($entityManager)) {
            throw new DoctrineCheckerException(500, 'Doctrine entity manager not found in container. Have you installed or enabled Doctrine?');
        }
        if (!$entityManager instanceof EntityManagerInterface) {
            throw new DoctrineCheckerException(500, 'Doctrine entity manager service is not an instance of EntityManager.');
        }
        $this->entityManager = $entityManager;
    }
}
