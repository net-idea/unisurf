<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\FormContactEntity;
use App\Repository\FormContactRepository;
use App\Tests\DatabaseTestCase;

class ContactFormIntegrationTest extends DatabaseTestCase
{
    public function testCompleteContactFormSubmissionFlow(): void
    {
        $client = static::createClient();

        // Step 1: Visit the contact page to get CSRF token
        $crawler = $client->request('GET', '/kontakt');
        $this->assertResponseIsSuccessful();
        $csrfToken = $crawler->filter('input[name="form_contact[_token]"]')->attr('value');

        // Step 2: Submit via API
        $client->request(
            'POST',
            '/api/contact',
            [
                'form_contact' => [
                    'name'    => 'Integration Test User',
                    'email'   => 'integration@test.com',
                    'phone'   => '+49 123 456789',
                    'message' => 'This is an integration test message with more than 10 characters',
                    'consent' => '1',
                    '_token'  => $csrfToken,
                ],
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
        );

        // Step 3: Check JSON response
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('success', $response['status']);

        // Step 4: Verify data was saved to database
        $container = static::getContainer();

        /** @var FormContactRepository $repository */
        $repository = $container->get(FormContactRepository::class);

        // Clear entity manager to ensure we fetch from database
        $this->getEntityManager()->clear();

        $submissions = $repository->findBy(
            ['emailAddress' => 'integration@test.com'],
            ['id' => 'DESC'],
            1,
        );
        $this->assertCount(1, $submissions, 'Should have exactly one submission saved to database');

        /** @var FormContactEntity $submission */
        $submission = $submissions[0];
        $this->assertSame('Integration Test User', $submission->getName());
        $this->assertSame('integration@test.com', $submission->getEmailAddress());
        $this->assertSame('+49 123 456789', $submission->getPhone());
        $this->assertStringContainsString(
            'integration test message',
            strtolower($submission->getMessage()),
        );
        $this->assertTrue($submission->getConsent());

        // Verify metadata was saved
        $meta = $submission->getMeta();
        $this->assertNotNull($meta, 'Submission should have metadata');
        $this->assertNotEmpty($meta->getIp());
        $this->assertNotEmpty($meta->getUserAgent());
        $this->assertNotEmpty($meta->getTime());
    }

    public function testRateLimitingPreventsSpam(): void
    {
        $client = static::createClient();

        // Step 1: Get CSRF token
        $crawler = $client->request('GET', '/kontakt');
        $csrfToken = $crawler->filter('input[name="form_contact[_token]"]')->attr('value');

        // Helper to simplify submission
        $submitForm = function () use ($client, $csrfToken): void {
            $client->request(
                'POST',
                '/api/contact',
                [
                    'form_contact' => [
                        'name'    => 'Spammer User',
                        'email'   => 'spammer@example.com',
                        'message' => 'This is a spam message integration test.',
                        'consent' => '1',
                        '_token'  => $csrfToken,
                    ],
                ],
                [],
                ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
            );
        };

        // Step 2: Fill the rate limit window
        // Limit is 10 per hour or 1 per 30s. The test loop logic depends on config.
        // AbstractFormService defines:
        // const RATE_MIN_INTERVAL_SECONDS = 30;
        // So submitting twice immediately should trigger rate limit.

        // First submission: Success
        $submitForm();
        $this->assertResponseIsSuccessful();

        // Second submission immediately: Should be blocked
        $submitForm();
        $this->assertResponseStatusCodeSame(429);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('error', $response['status']);
        $this->assertSame('rate', $response['code']);
    }
}
