<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('dashboard:update-stats')->everySixHours();
Schedule::command('app:aggregate-website-visits')->dailyAt('01:00');
Schedule::command('app:cleanup-user-activities')->dailyAt('02:00');
