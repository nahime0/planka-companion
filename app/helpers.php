<?php

use App\Services\PlankaUrlService;

if (!function_exists('planka')) {
    /**
     * Get the Planka URL service instance
     */
    function planka(): PlankaUrlService
    {
        return app(PlankaUrlService::class);
    }
}