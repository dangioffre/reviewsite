<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule VOD refresh for all streamers every 6 hours
Schedule::command('streamer:refresh-vods --limit=100')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground();

// Schedule VOD health checks daily
Schedule::command('streamer:check-vod-health')
    ->daily()
    ->withoutOverlapping()
    ->runInBackground();

// Schedule OAuth token refresh every 12 hours
Schedule::command('streamer:refresh-oauth-tokens')
    ->twiceDaily()
    ->withoutOverlapping()
    ->runInBackground();

// Schedule VOD sync for all streamers every 6 hours
Schedule::command('streamer:sync-all-vods')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground();

// Schedule stale VOD cleanup weekly
Schedule::command('streamer:cleanup-stale-vods')
    ->weekly()
    ->withoutOverlapping()
    ->runInBackground();

// Schedule profile updates daily
Schedule::command('streamer:update-profiles')
    ->daily()
    ->withoutOverlapping()
    ->runInBackground();

// Schedule job health checks every hour
Schedule::command('streamer:check-job-health')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
