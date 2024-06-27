<?php

namespace Zu\HealthCheckBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthCheckControllerTest extends WebTestCase
{
    public function testPing(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ping');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'pong');
    }

    public function testHealthCheck(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health-check');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonStringEqualsJsonString('{"status":"ok"}', $client->getResponse()->getContent());
    }
}