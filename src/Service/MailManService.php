<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\FormBookingEntity;
use App\Entity\FormContactEntity;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment as Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class MailManService
{
    private const THEME_STORAGE_KEY = 'theme';

    public function __construct(
        private MailerInterface $mailer,
        private Twig $twig,
        private string $fromAddress,
        private string $fromName,
        private string $toAddress,
        private string $toName,
        private LoggerInterface $logger,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function sendContactForm(FormContactEntity $contact): void
    {
        $from = new Address($this->fromAddress, $this->fromName);
        $to = new Address($this->toAddress, $this->toName);
        $theme = $this->getEmailTheme();

        $context = [
            'contact' => $contact,
            'theme'   => $theme,
        ];

        try {
            // Send email to owner (always use light theme for admin emails)
            $ownerSubject = 'UniSurf — Neue Kontaktanfrage';
            $ownerText = $this->twig->render('email/contact_owner.txt.twig', $context);
            $ownerHtml = $this->twig->render('email/contact_owner.html.twig', $context);

            $emailOwner = (new Email())
                ->from($from)
                ->to($to)
                ->replyTo(new Address($contact->getEmailAddress(), $contact->getName()))
                ->subject($ownerSubject)
                ->text($ownerText)
                ->html($ownerHtml);

            $this->mailer->send($emailOwner);
            $this->logger->info('Contact mail sent to owner', [
                'to'    => $to->getAddress(),
                'name'  => $to->getName(),
                'email' => $contact->getEmailAddress(),
                'theme' => $theme,
            ]);

            // Send copy to visitor with their preferred theme
            if ($contact->getCopy()) {
                $visitorSubject = 'UniSurf — Ihre Kontaktanfrage';
                $visitorText = $this->twig->render('email/contact_visitor.txt.twig', $context);
                $visitorHtml = $this->twig->render('email/contact_visitor.html.twig', $context);

                $emailVisitor = (new Email())
                    ->from($from)
                    ->to(new Address($contact->getEmailAddress(), $contact->getName()))
                    ->subject($visitorSubject)
                    ->text($visitorText)
                    ->html($visitorHtml);

                $this->mailer->send($emailVisitor);
                $this->logger->info('Contact mail sent to visitor', [
                    'to'    => $contact->getEmailAddress(),
                    'name'  => $contact->getName(),
                    'theme' => $theme,
                ]);
            }
        } catch (TransportExceptionInterface $e) {
            // Logs transport failures (bad DSN, auth, SSL, DNS, etc.)
            $this->logger->error('Mailer send failed: ' . $e->getMessage(), ['exception' => $e]);

            throw $e;
        }
    }

    /**
     * Send a confirmation request to the visitor with a unique link.
     *
     * @throws TransportExceptionInterface|RuntimeError|LoaderError|SyntaxError
     */
    public function sendBookingVisitorConfirmationRequest(FormBookingEntity $booking, string $confirmUrl): void
    {
        $from = new Address($this->fromAddress, $this->fromName);
        $toVisitor = new Address($booking->getEmail(), $booking->getName());

        $context = [
            'booking'    => $booking,
            'confirmUrl' => $confirmUrl,
        ];

        // Log before attempting to render or send
        $this->logger->info(
            'Preparing booking confirmation request',
            [
                'to'    => $toVisitor->getAddress(),
                'name'  => $toVisitor->getName(),
                'token' => substr($booking->getConfirmationToken(), 0, 6) . '…',
            ]
        );

        try {
            $subject = 'UniSurf — Bitte bestätigen Sie Ihre Buchung';
            $text = $this->twig->render('email/booking_visitor_confirm_request.txt.twig', $context);
            $html = $this->twig->render('email/booking_visitor_confirm_request.html.twig', $context);

            $email = (new Email())
                ->from($from)
                ->to($toVisitor)
                ->replyTo(new Address($this->toAddress, $this->toName))
                ->subject($subject)
                ->text($text)
                ->html($html);

            $this->mailer->send($email);
            $this->logger->info('Booking confirmation request sent successfully', [
                'to'        => $toVisitor->getAddress(),
                'bookingId' => $booking->getId(),
            ]);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Mailer transport failed', [
                'exception' => $e->getMessage(),
                'to'        => $toVisitor->getAddress(),
                'bookingId' => $booking->getId(),
            ]);

            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('Email preparation or sending failed', [
                'exception' => $e->getMessage(),
                'to'        => $toVisitor->getAddress(),
                'bookingId' => $booking->getId(),
            ]);

            throw $e;
        }
    }

    /**
     * Notify the owner when a booking was confirmed by the visitor.
     *
     * @throws TransportExceptionInterface|RuntimeError|LoaderError|SyntaxError
     */
    public function sendBookingOwnerNotification(FormBookingEntity $booking): void
    {
        $from = new Address($this->fromAddress, $this->fromName);
        $toOwner = new Address($this->toAddress, $this->toName);

        $context = ['booking' => $booking];

        $subject = 'UniSurf — Buchung bestätigt';
        $text = $this->twig->render('email/booking_owner_confirmed.txt.twig', $context);
        $html = $this->twig->render('email/booking_owner_confirmed.html.twig', $context);

        $email = (new Email())
            ->from($from)
            ->to($toOwner)
            ->replyTo(new Address($booking->getEmail(), $booking->getName()))
            ->subject($subject)
            ->text($text)
            ->html($html);

        $this->logger->info('Sending booking owner notification', [
            'to'        => $toOwner->getAddress(),
            'name'      => $toOwner->getName(),
            'bookingId' => $booking->getId(),
        ]);

        try {
            $this->mailer->send($email);
            $this->logger->info('Booking notification sent to owner');
        } catch (TransportExceptionInterface $e) {
            // Logs transport failures (bad DSN, auth, SSL, DNS, etc.)
            $this->logger->error('Mailer send failed: ' . $e->getMessage(), ['exception' => $e]);

            throw $e;
        }
    }

    /**
     * Determine which email theme to use based on user's preference
     */
    private function getEmailTheme(): string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return 'light';
        }

        $session = $request->getSession();
        $storedTheme = $session->get(self::THEME_STORAGE_KEY);

        // If user explicitly chose dark or light, use that
        if ('dark' === $storedTheme) {
            return 'dark';
        }

        if ('light' === $storedTheme) {
            return 'light';
        }

        // If 'system' or not set, check User-Agent for dark mode preference
        // Note: This is a fallback - in practice, the localStorage value should be used
        $userAgent = $request->headers->get('User-Agent', '');

        // Default to light theme for emails
        return 'light';
    }
}
