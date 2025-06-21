<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Posts', Post::count())
                ->description('All posts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Featured Posts', Post::where('is_featured', true)->count())
                ->description('Posts in slider')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
} 