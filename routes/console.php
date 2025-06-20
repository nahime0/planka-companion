<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule card expiration notifications
Schedule::command('cards:notify-expiring')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
