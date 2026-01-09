<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\FormContactEntity;
use App\Service\FormContactService;
use App\Service\MailManService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

class FormContactServiceTest extends TestCase
{
    public function testFormDataIsRestoredFromSession(): void
    {
        // Seed session with previously entered data
        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $session->set('cf_data', [
            'name'         => 'Alice',
            'emailAddress' => 'alice@example.com',
            'phone'        => '123',
            'message'      => 'Hi there',
            'consent'      => true,
            'copy'         => true,
        ]);

        $request = new Request();
        $request->setSession($session);
        $stack = new RequestStack();
        $stack->push($request);

        $svc = $this->makeService($stack);

        $form = $svc->getForm();
        $data = $form->getData();
        $this->assertInstanceOf(FormContactEntity::class, $data);
        $this->assertSame('Alice', $data->getName());
        $this->assertSame('alice@example.com', $data->getEmailAddress());
        $this->assertSame('123', $data->getPhone());
        $this->assertSame('Hi there', $data->getMessage());
        $this->assertTrue($data->getConsent());
        $this->assertTrue($data->getCopy());

        // Ensure data is one-time restored (removed after build)
        $form2 = $svc->getForm();
        $this->assertSame($form, $form2, 'Form instance is cached for the request lifecycle');
    }

    public function testHandleReturnsNullForGetRequest(): void
    {
        $stack = new RequestStack();
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $request->setSession($session);
        $stack->push($request);

        $svc = $this->makeService($stack);
        $result = $svc->handle();

        $this->assertNull($result, 'handle() should return null for GET requests');
    }

    public function testHandleReturnsNullForInvalidFormSubmission(): void
    {
        $stack = new RequestStack();
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        // Create a POST request with empty data
        $request = new Request([], [
            'form_contact' => [
                'name'    => '',
                'email'   => '',
                'message' => '',
                'consent' => false,
                '_token'  => 'dummy',
            ],
        ], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $request->setSession($session);
        $stack->push($request);

        $svc = $this->makeService($stack);
        $result = $svc->handle();

        // Should return null to let controller re-render with errors
        $this->assertNull($result, 'handle() should return null for invalid form data');

        // Form should have errors
        $form = $svc->getForm();
        $this->assertFalse($form->isValid());
    }

    public function testHandleRedirectsForHoneypotTrap(): void
    {
        $stack = new RequestStack();
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        // Create POST request with honeypot filled
        $request = new Request([], [
            'form_contact' => [
                'name'    => 'Spammer',
                'email'   => 'spam@example.com',
                'phone'   => '123',
                'message' => 'This is a spam message',
                'consent' => true,
                'website' => 'http://spam-site.com', // Honeypot filled!
                '_token'  => 'dummy',
            ],
        ], [], [], [], ['REQUEST_METHOD' => 'POST', 'REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'TestBot']);
        $request->setSession($session);
        $stack->push($request);

        $urls = $this->createMock(UrlGeneratorInterface::class);
        $urls->method('generate')->willReturn('/kontakt?submit=1');

        $svc = $this->makeServiceWithUrls($stack, $urls);
        $result = $svc->handle();

        // Should redirect (pretending success)
        $this->assertNotNull($result);
        $this->assertStringContainsString('submit=1', $result->getTargetUrl());
    }

    public function testHandleRedirectsForRateLimit(): void
    {
        $stack = new RequestStack();
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        // Simulate rate limit by adding recent timestamps
        $now = time();
        $session->set('cf_times', [$now - 1, $now - 2, $now - 3]);

        // Create POST request
        $request = new Request([], [
            'form_contact' => [
                'name'    => 'John Doe',
                'email'   => 'john@example.com',
                'message' => 'Valid message with more than 10 chars',
                'consent' => true,
                '_token'  => 'dummy',
            ],
        ], [], [], [], ['REQUEST_METHOD' => 'POST', 'REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'TestBrowser']);
        $request->setSession($session);
        $stack->push($request);

        $urls = $this->createMock(UrlGeneratorInterface::class);
        $urls->method('generate')->willReturn('/kontakt?error=rate');

        $svc = $this->makeServiceWithUrls($stack, $urls);
        $result = $svc->handle();

        // Should redirect with rate error
        $this->assertNotNull($result);
        $this->assertStringContainsString('error=rate', $result->getTargetUrl());
    }

    public function testHandleRedirectsOnMailSendFailure(): void
    {
        $stack = new RequestStack();
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        // Create valid POST request
        $request = new Request([], [
            'form_contact' => [
                'name'    => 'John Doe',
                'email'   => 'john@example.com',
                'phone'   => '123456',
                'message' => 'Valid message with more than 10 chars',
                'consent' => true,
                '_token'  => 'dummy',
            ],
        ], [], [], [], ['REQUEST_METHOD' => 'POST', 'REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'TestBrowser']);
        $request->setSession($session);
        $stack->push($request);

        $urls = $this->createMock(UrlGeneratorInterface::class);
        $urls->method('generate')->willReturn('/kontakt?error=mail');

        // Mock MailManService to throw exception
        $mailMan = $this->createMock(MailManService::class);
        $mailMan->method('sendContactForm')
            ->willThrowException($this->createMock(TransportExceptionInterface::class));

        $svc = $this->makeServiceWithMocks($stack, $urls, $mailMan);
        $result = $svc->handle();

        // Should redirect with mail error
        $this->assertNotNull($result);
        $this->assertStringContainsString('error=mail', $result->getTargetUrl());
    }

    public function testHandleRedirectsOnSuccessfulSubmission(): void
    {
        $stack = new RequestStack();
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        // Create valid POST request
        $request = new Request([], [
            'form_contact' => [
                'name'    => 'John Doe',
                'email'   => 'john@example.com',
                'phone'   => '123456',
                'message' => 'Valid message with more than 10 chars',
                'consent' => true,
                '_token'  => 'dummy',
            ],
        ], [], [], [], ['REQUEST_METHOD' => 'POST', 'REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'TestBrowser']);
        $request->setSession($session);
        $stack->push($request);

        $urls = $this->createMock(UrlGeneratorInterface::class);
        $urls->method('generate')->willReturn('/kontakt?submit=1');

        // Mock MailManService to succeed
        $mailMan = $this->createMock(MailManService::class);
        $mailMan->expects($this->once())
            ->method('sendContactForm');

        $svc = $this->makeServiceWithMocks($stack, $urls, $mailMan);
        $result = $svc->handle();

        // Should redirect with success
        $this->assertNotNull($result);
        $this->assertStringContainsString('submit=1', $result->getTargetUrl());

        // Session data should be cleared after success
        $this->assertFalse($session->has('cf_data'));
    }

    public function testFormValidationForAllRequiredFields(): void
    {
        $stack = new RequestStack();
        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $request = new Request();
        $request->setSession($session);
        $stack->push($request);

        $svc = $this->makeService($stack);
        $form = $svc->getForm();

        // Submit empty form
        $form->submit([
            'name'    => '',
            'email'   => '',
            'message' => '',
            'consent' => false,
        ]);

        $this->assertFalse($form->isValid());
        $this->assertCount(4, $form->getErrors(true)); // 4 required field errors
    }

    public function testFormValidationForInvalidEmail(): void
    {
        $stack = new RequestStack();
        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $request = new Request();
        $request->setSession($session);
        $stack->push($request);

        $svc = $this->makeService($stack);
        $form = $svc->getForm();

        $form->submit([
            'name'    => 'John Doe',
            'email'   => 'invalid-email',
            'message' => 'Valid message',
            'consent' => true,
        ]);

        $this->assertFalse($form->isValid());
        $emailErrors = $form->get('email')->getErrors();
        $this->assertGreaterThan(0, count($emailErrors));
    }

    public function testFormValidationForShortMessage(): void
    {
        $stack = new RequestStack();
        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $request = new Request();
        $request->setSession($session);
        $stack->push($request);

        $svc = $this->makeService($stack);
        $form = $svc->getForm();

        $form->submit([
            'name'    => 'John Doe',
            'email'   => 'john@example.com',
            'message' => 'Short', // Less than 10 characters
            'consent' => true,
        ]);

        $this->assertFalse($form->isValid());
        $messageErrors = $form->get('message')->getErrors();
        $this->assertGreaterThan(0, count($messageErrors));
    }

    private function makeFormFactory(): FormFactoryInterface
    {
        $csrf = new CsrfTokenManager();
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        return Forms::createFormFactoryBuilder()
            ->addExtension(new CsrfExtension($csrf))
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();
    }

    private function makeService(RequestStack $stack): FormContactService
    {
        $forms = $this->makeFormFactory();
        $mailMan = $this->createMock(MailManService::class);
        $urls = $this->createMock(UrlGeneratorInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);

        return new FormContactService($forms, $stack, $mailMan, $urls, $em);
    }

    private function makeServiceWithUrls(RequestStack $stack, UrlGeneratorInterface $urls): FormContactService
    {
        $forms = $this->makeFormFactory();
        $mailMan = $this->createMock(MailManService::class);
        $em = $this->createMock(EntityManagerInterface::class);

        return new FormContactService($forms, $stack, $mailMan, $urls, $em);
    }

    private function makeServiceWithMocks(RequestStack $stack, UrlGeneratorInterface $urls, MailManService $mailMan): FormContactService
    {
        $forms = $this->makeFormFactory();
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist');
        $em->method('flush');

        return new FormContactService($forms, $stack, $mailMan, $urls, $em);
    }
}
