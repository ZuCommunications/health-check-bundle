<?php

namespace Zu\HealthCheckBundle\Enum;

enum CheckStatusEnum: string
{
    case CONNECTION_OK = 'OK';
    case CONNECTION_ERROR = 'ERROR';
    case CONNECTION_FAIL = 'FAIL';
}
