<?php

namespace App\Filament\Resources\PodcastResource\Widgets;

use App\Models\Podcast;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PodcastStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPodcasts = Podcast::count();
        $approvedPodcasts = Podcast::where('status', 'approved')->count();
        $pendingPodcasts = Podcast::where('status', 'pending')->count();
        $verifiedPodcasts = Podcast::where('status', 'verified')->count();
        $rejectedPodcasts = Podcast::where('status', 'rejected')->count();
        
        // Get submissions from last 30 days
        $recentSubmissions = Podcast::where('created_at', '>=', now()->subDays(30))->count();
        
        // Get RSS errors count
        $rssErrors = Podcast::whereNotNull('rss_error')->count();
        
        // Get approval rate
        $approvalRate = $totalPodcasts > 0 ? round(($approvedPodcasts / $totalPodcasts) * 100, 1) : 0;
        
        return [
            Stat::make('Total Podcasts', $totalPodcasts)
                ->description('All submitted podcasts')
                ->color('primary')
                ->icon('heroicon-o-microphone'),
                
            Stat::make('Approved', $approvedPodcasts)
                ->description('Live on the platform')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
                
            Stat::make('Pending Review', $pendingPodcasts + $verifiedPodcasts)
                ->description($pendingPodcasts . ' pending, ' . $verifiedPodcasts . ' verified')
                ->color('warning')
                ->icon('heroicon-o-clock'),
                
            Stat::make('Recent Submissions', $recentSubmissions)
                ->description('Last 30 days')
                ->color('info')
                ->icon('heroicon-o-calendar-days'),
                
            Stat::make('Approval Rate', $approvalRate . '%')
                ->description('Approved vs total submissions')
                ->color($approvalRate >= 80 ? 'success' : ($approvalRate >= 60 ? 'warning' : 'danger'))
                ->icon('heroicon-o-chart-bar'),
                
            Stat::make('RSS Errors', $rssErrors)
                ->description('Podcasts with feed issues')
                ->color($rssErrors > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
} 