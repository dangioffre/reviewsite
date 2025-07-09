<?php

namespace App\Filament\Resources\EpisodeResource\Pages;

use App\Filament\Resources\EpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEpisode extends ViewRecord
{
    protected static string $resource = EpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('visitEpisode')
                ->label('Visit Episode')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->visible(fn (): bool => $this->record->podcast->status === 'approved')
                ->url(fn (): string => route('podcasts.episodes.show', [$this->record->podcast, $this->record]))
                ->openUrlInNewTab(),
        ];
    }
} 