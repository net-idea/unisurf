<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FormContactService;
use App\Service\NavigationService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractBaseController
{
    public function __construct(
        private readonly NavigationService $navigation,
        private readonly FormContactService $formContactService,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[Route(
        path: '/kontakt',
        name: 'app_contact',
        methods: ['GET', 'POST']
    )]
    public function contact(): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        // Handle success/error flash messages from redirects
        if ($request && $request->query->has('submit')) {
            $this->addFlash('success', 'Vielen Dank für Ihre Nachricht! Wir haben Ihre Anfrage erhalten und werden uns so schnell wie möglich bei Ihnen melden.');
        }

        if ($request && $request->query->has('error')) {
            $errorType = $request->query->get('error');
            if ('mail' === $errorType) {
                $this->addFlash('error', 'Leider konnte Ihre Nachricht nicht versendet werden. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt per E-Mail.');
            } elseif ('rate' === $errorType) {
                $this->addFlash('error', 'Sie haben zu viele Anfragen in kurzer Zeit gesendet. Bitte warten Sie einen Moment, bevor Sie es erneut versuchen.');
            } else {
                $this->addFlash('error', 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
            }
        }

        $form = $this->formContactService->getForm();

        if ($response = $this->formContactService->handle()) {
            return $response;
        }

        return $this->render(
            'pages/kontakt.html.twig',
            [
                'slug'     => 'kontakt',
                'navItems' => $this->navigation->getItems(),
                'form'     => $form->createView(),
            ]
        );
    }
}
