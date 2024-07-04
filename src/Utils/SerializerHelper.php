<?php

namespace Zu\HealthCheckBundle\Utils;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerHelper
{
    public static function createSerializer(): Serializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [
            new BackedEnumNormalizer(),
            // adding the ReflectionExtractor so that we can deserialize enums, without this the deserialization will fail
            new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
        ];

        return new Serializer($normalizers, $encoders);
    }
}
