<?php

use App\Services\PlankaUrlService;
use App\Services\TelegramNotificationService;

if (!function_exists('planka')) {
    /**
     * Get the Planka URL service instance
     */
    function planka(): PlankaUrlService
    {
        return app(PlankaUrlService::class);
    }
}

if (!function_exists('telegram')) {
    /**
     * Get the Telegram notification service instance
     */
    function telegram(): TelegramNotificationService
    {
        return app(TelegramNotificationService::class);
    }
}