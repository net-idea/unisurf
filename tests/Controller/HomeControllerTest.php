<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomepageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Willkommen bei UniSurf');
    }

    public function testServicesPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/services');

        $this->assertResponseIsSuccessful();
    }

    public function testEntwicklungPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/entwicklung');

        $this->assertResponseIsSuccessful();
    }

    public function testHostingPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/hosting');

        $this->assertResponseIsSuccessful();
    }

    public function testImpressumPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/impressum');

        $this->assertResponseIsSuccessful();
    }

    public function testDatenschutzPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/datenschutz');

        $this->assertResponseIsSuccessful();
    }
}
