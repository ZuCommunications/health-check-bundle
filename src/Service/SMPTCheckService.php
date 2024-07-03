<?php

namespace Zu\HealthCheckBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;

class SMPTCheckService extends AbstractChecker
{
    public function __construct(
        private MailerInterface $mailer
    )
    {
        parent::__construct();
    }

    public function check(): JsonResponse
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Test')
            ->text('Test');

        try {
            $this->mailer->send($email);
            $this->data->setStatus(CheckStatusEnum::CONNECTION_OK);
        } catch(\Exception $e) {
            $this->data->setStatus(CheckStatusEnum::CONNECTION_ERROR);
            $this->data->setMessage(AbstractChecker::$CONNECTION_ERROR_MESSAGE);
        }
        return $this->createResponse();
    }

    public function getName(): string
    {
        return 'SMPT';
    }
}