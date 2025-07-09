<?php

namespace Zu\HealthCheckBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Zu\HealthCheckBundle\Objects\Data;
use Zu\HealthCheckBundle\Utils\SerializerHelper;

abstract class AbstractChecker implements CheckerInterface
{
    protected Data $data;

    public static string $CONNECTION_FAILED_MESSAGE = 'Connection failed';
    public static string $CONNECTION_ERROR_MESSAGE = 'Could not connect. Check Application Logs';

    public function __construct()
    {
        $this->data = new Data($this->getName());
    }

    /**
     * This method is used to create the response that will be returned by the checker in json.
     */
    public function createResponse(): JsonResponse
    {
        $serializer = SerializerHelper::createSerializer();

        return JsonResponse::fromJsonString($serializer->serialize($this->data, 'json'));
    }
}
