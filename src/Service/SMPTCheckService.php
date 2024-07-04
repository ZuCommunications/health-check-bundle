<?php

namespace Zu\HealthCheckBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Zu\HealthCheckBundle\Enum\CheckStatusEnum;
use Zu\HealthCheckBundle\Exception\SMTPCheckerException;

class SMPTCheckService extends AbstractChecker
{
    private MailerInterface $mailer;

    public function __construct(
        private readonly ContainerInterface $container
    ) {
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
        } catch (\Exception $e) {
            $this->data->setStatus(CheckStatusEnum::CONNECTION_ERROR);
            $this->data->setMessage(AbstractChecker::$CONNECTION_ERROR_MESSAGE);
        }

        return $this->createResponse();
    }

    public function getName(): string
    {
        return 'SMPT';
    }

    protected function getService(): void
    {
        $mailer = $this->container->get('mailer.mailer');
        if (!isset($mailer)) {
            throw new SMTPCheckerException(500, 'Mailer not found in container. Have you installed or enabled Mailer?');
        }
        if (!$mailer instanceof MailerInterface) {
            throw new SMTPCheckerException(500, 'Mailer service is not an instance of Mailer.');
        }
        $this->mailer = $mailer;
    }
}
