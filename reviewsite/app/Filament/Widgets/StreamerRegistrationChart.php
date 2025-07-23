<?php

namespace App\Filament\Widgets;

use App\Models\StreamerProfile;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StreamerRegistrationChart extends ChartWidget
{
    protected static ?string $heading = 'Streamer Registrations (Last 6 Months)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = StreamerProfile::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Streamer Registrations',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('month')->map(function ($month) {
                return date('M Y', strtotime($month . '-01'));
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}