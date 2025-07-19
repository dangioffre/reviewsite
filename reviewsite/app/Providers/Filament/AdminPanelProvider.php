<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => [
                    50 => '#fef2f2',
                    100 => '#fee2e2',
                    200 => '#fecaca',
                    300 => '#fca5a5',
                    400 => '#f87171',
                    500 => '#ef4444', // Main red color
                    600 => '#dc2626', // Darker red
                    700 => '#b91c1c',
                    800 => '#991b1b',
                    900 => '#7f1d1d',
                    950 => '#450a0a',
                ],
            ])
            ->darkMode(true)
            ->brandName('ReviewSite Admin')
            ->brandLogo(asset('favicon.ico'))
            ->brandLogoHeight('2rem')
            ->font('Inter')
            ->renderHook(
                'panels::head.end',
                fn (): string => '<style>
                    .fi-sidebar { background: linear-gradient(135deg, #1a1a1b 0%, #27272a 100%) !important; border-right: 1px solid #3f3f46 !important; }
                    .fi-topbar { background: linear-gradient(135deg, #1a1a1b 0%, #27272a 100%) !important; border-bottom: 1px solid #3f3f46 !important; }
                    .fi-sidebar-nav-item:hover { background: rgba(239, 68, 68, 0.1) !important; }
                    .fi-sidebar-nav-item[aria-current="page"] { background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.2) 100%) !important; border-right: 3px solid #ef4444 !important; }
                    .fi-sidebar-nav-item-badge { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important; color: white !important; font-weight: bold !important; }
                </style>'
            )
            ->resources([
                \App\Filament\Resources\ReportResource::class,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\StreamerStatsOverview::class,
                \App\Filament\Widgets\StreamerRegistrationChart::class,
                \App\Filament\Widgets\RecentStreamerActivity::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\AdminMiddleware::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Products'),
                NavigationGroup::make('Content'),
                NavigationGroup::make('Community'),
                NavigationGroup::make('Streamers'),
                NavigationGroup::make('Categories'),
                NavigationGroup::make('Companies'),
            ]);
    }
}
