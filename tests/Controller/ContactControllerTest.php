<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DatabaseTestCase;

class ContactControllerTest extends DatabaseTestCase
{
    public function testContactPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/kontakt');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Nachricht senden');
    }

    public function testFormHasAllRequiredFields(): void
    {
        $client = static::createClient();
        $client->request('GET', '/kontakt');

        $this->assertSelectorExists('input[name="form_contact[name]"]');
        $this->assertSelectorExists('input[name="form_contact[email]"]');
        $this->assertSelectorExists('input[name="form_contact[phone]"]');
        $this->assertSelectorExists('textarea[name="form_contact[message]"]');
        $this->assertSelectorExists('input[name="form_contact[consent]"]');
        $this->assertSelectorExists('input[name="form_contact[copy]"]');
        $this->assertSelectorExists('input[name="form_contact[_token]"]');
    }

    public function testFormHasHoneypotFields(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/kontakt');

        $emailrepField = $crawler->filter('input[name="form_contact[emailrep]"]');
        $websiteField = $crawler->filter('input[name="form_contact[website]"]');

        $this->assertSelectorExists('input[name="form_contact[emailrep]"]');
        $this->assertSelectorExists('input[name="form_contact[website]"]');

        $this->assertStringContainsString('visually-hidden', $emailrepField->attr('class'));
        $this->assertStringContainsString('visually-hidden', $websiteField->attr('class'));
    }

    public function testApiSubmitEmptyFormReturnsErrors(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/kontakt');
        $csrfToken = $crawler->filter('input[name="form_contact[_token]"]')->attr('value');

        $client->request(
            'POST',
            '/api/contact',
            ['form_contact' => ['_token' => $csrfToken]],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
        );

        $this->assertResponseStatusCodeSame(422);
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('error', $response['status']);
        $this->assertSame('invalid', $response['code']);

        // Check errors presence (simplified checks)
        $this->assertArrayHasKey('name', $response['errors']);
        $this->assertArrayHasKey('email', $response['errors']);
        $this->assertArrayHasKey('message', $response['errors']);
        $this->assertArrayHasKey('consent', $response['errors']);
    }

    public function testApiSubmitWithOnlyNameReturnsOtherErrors(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/kontakt');
        $csrfToken = $crawler->filter('input[name="form_contact[_token]"]')->attr('value');

        $client->request(
            'POST',
            '/api/contact',
            [
                'form_contact' => [
                    'name'   => 'John Doe',
                    '_token' => $csrfToken,
                ],
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
        );

        $this->assertResponseStatusCodeSame(422);
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayNotHasKey('name', $response['errors']);
        $this->assertArrayHasKey('email', $response['errors']);
    }

    public function testApiSubmitValidFormReturnsSuccess(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/kontakt');
        $csrfToken = $crawler->filter('input[name="form_contact[_token]"]')->attr('value');

        $client->request(
            'POST',
            '/api/contact',
            [
                'form_contact' => [
                    'name'    => 'John API',
                    'email'   => 'api@example.com',
                    'message' => 'This is an API test message with sufficient length.',
                    'consent' => '1',
                    'phone'   => '+49 123 456789',
                    '_token'  => $csrfToken,
                ],
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
        );

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('success', $response['status']);
    }

    public function testApiHoneypotStrategies(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/kontakt');
        $csrfToken = $crawler->filter('input[name="form_contact[_token]"]')->attr('value');

        // Case 1: Fill website (honeypot)
        $client->request(
            'POST',
            '/api/contact',
            [
                'form_contact' => [
                    'name'    => 'Spam',
                    'email'   => 'spam@example.com',
                    'message' => 'Spam content',
                    'consent' => '1',
                    'website' => 'http://spam.com',
                    '_token'  => $csrfToken,
                ],
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
        );

        // Expect fake success
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('success', $response['status']);
    }

    public function testApiInvalidCsrfToken(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/contact',
            [
                'form_contact' => [
                    'name'    => 'John',
                    'message' => 'Test',
                    '_token'  => 'invalid_token',
                ],
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
        );

        // CSRF failure usually results in validation error on the token field or form level
        // or a 422 because the form is invalid
        $this->assertResponseStatusCodeSame(422);
    }
}
