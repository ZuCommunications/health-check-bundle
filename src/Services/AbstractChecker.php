<?php

namespace Zu\HealthCheckBundle\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Zu\HealthCheckBundle\Objects\Data;
use Zu\HealthCheckBundle\Services\CheckerInterface;

abstract class AbstractChecker implements CheckerInterface
{
    protected Data $data;

    public static $CONNECTION_OK = 'OK';
    public static $CONNECTION_FAIL = 'FAIL';
    public static $CONNECTION_ERROR = 'ERROR';

    public static $CONNECTION_FAILED_MESSAGE = 'Connection failed';
    public static $CONNECTION_ERROR_MESSAGE = 'Could not connect. Check Application Logs';
    public function __construct()
    {
        $this->data = new Data($this->getName());
    }

    function createResponse(): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return JsonResponse::fromJsonString($serializer->serialize($this->data, 'json'));
    }
}