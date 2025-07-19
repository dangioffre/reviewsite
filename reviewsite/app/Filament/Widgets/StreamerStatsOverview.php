<?php

namespace App\Filament\Widgets;

use App\Models\StreamerProfile;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StreamerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalStreamers = StreamerProfile::count();
        $approvedStreamers = StreamerProfile::approved()->count();
        $verifiedStreamers = StreamerProfile::verified()->count();
        $pendingApproval = StreamerProfile::where('is_approved', false)->count();
        $streamerReviews = Review::whereNotNull('streamer_profile_id')->count();
        
        // Platform breakdown
        $twitchStreamers = StreamerProfile::platform('twitch')->count();
        $youtubeStreamers = StreamerProfile::platform('youtube')->count();
        $kickStreamers = StreamerProfile::platform('kick')->count();

        return [
            Stat::make('Total Streamers', $totalStreamers)
                ->description('All registered streamers')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('primary'),

            Stat::make('Approved Streamers', $approvedStreamers)
                ->description('Publicly visible profiles')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Verified Streamers', $verifiedStreamers)
                ->description('Channel ownership verified')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('info'),

            Stat::make('Pending Approval', $pendingApproval)
                ->description('Awaiting admin review')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingApproval > 0 ? 'warning' : 'success'),

            Stat::make('Streamer Reviews', $streamerReviews)
                ->description('Reviews by streamers')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('Platform Distribution', '')
                ->description("Twitch: {$twitchStreamers} | YouTube: {$youtubeStreamers} | Kick: {$kickStreamers}")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('gray'),
        ];
    }
}