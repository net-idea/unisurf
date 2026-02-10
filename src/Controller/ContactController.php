<?php

declare(strict_types=1);

namespace App\Controller;

use NetIdea\WebBase\Controller\ContactController as BaseContactController;

/**
 * ContactController for the UniSurf website.
 *
 * Extends the base ContactController from the web-base bundle.
 * Override methods here to customize contact form behavior.
 *
 * Routes defined here have higher priority than bundle routes (priority: 0 vs -100).
 */
class ContactController extends BaseContactController
{
    // All functionality is inherited from the bundle.
    // Override methods here to customize behavior for this project.
    //
    // Example: Override the contact() method to add project-specific fields:
    //
    // public function contact(): Response
    // {
    //     // Add custom logic before rendering
    //     return parent::contact();
    // }
}
