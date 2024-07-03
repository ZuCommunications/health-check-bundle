<?php

namespace Zu\HealthCheckBundleTests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthCheckControllerTest extends WebTestCase
{
    private KernelBrowser $client;


    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testPing(): void
    {

        $this->client->request('GET', '/ping');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('pong', $this->client->getResponse()->getContent());
    }

    public function testHealthCheck(): void
    {
        $this->client->request('GET', '/health-check');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertStringContainsString('[]', $this->client->getResponse()->getContent());
    }
}