<?php

declare(strict_types=1);

namespace App\Controller;

use NetIdea\WebBase\Controller\MainController as BaseMainController;

/**
 * MainController for the UniSurf website.
 *
 * Extends the base MainController from the web-base bundle.
 * Override methods here to customize page rendering behavior.
 *
 * Routes defined here have higher priority than bundle routes (priority: 0 vs -100).
 */
class MainController extends BaseMainController
{
    // All functionality is inherited from the bundle.
    // Override methods here to customize behavior for this project.
    //
    // Example: Override the page() method to add project-specific logic:
    //
    // public function page(string $slug = 'index'): Response
    // {
    //     // Add custom logic before rendering
    //     return parent::page($slug);
    // }
}
