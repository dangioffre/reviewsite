<?php

namespace App\Filament\Resources\PodcastResource\Pages;

use App\Filament\Resources\PodcastResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewPodcast extends ViewRecord
{
    protected static string $resource = PodcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => in_array($this->record->status, ['verified', 'rejected']))
                ->requiresConfirmation()
                ->modalHeading('Approve Podcast')
                ->modalDescription('Are you sure you want to approve this podcast? It will be publicly visible.')
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('Podcast approved successfully')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => in_array($this->record->status, ['pending', 'verified']))
                ->form([
                    \Filament\Forms\Components\Textarea::make('admin_notes')
                        ->label('Rejection reason')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => 'rejected',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                        'admin_notes' => $data['admin_notes'],
                    ]);

                    Notification::make()
                        ->title('Podcast rejected')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('syncRss')
                ->label('Sync RSS')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->visible(fn (): bool => $this->record->status === 'approved')
                ->action(function (): void {
                    $service = app(\App\Services\RssVerificationService::class);
                    $count = $service->importEpisodes($this->record);
                    
                    if ($count > 0) {
                        Notification::make()
                            ->title("Imported {$count} new episodes")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('No new episodes found')
                            ->warning()
                            ->send();
                    }
                }),

            Actions\Action::make('verifyToken')
                ->label('Verify Token')
                ->icon('heroicon-o-shield-check')
                ->color('primary')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {
                    $service = app(\App\Services\RssVerificationService::class);
                    $verified = $service->verifyToken($this->record);
                    
                    if ($verified) {
                        Notification::make()
                            ->title('Token verification successful')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Token verification failed')
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('visitPodcast')
                ->label('Visit Podcast')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->visible(fn (): bool => $this->record->status === 'approved')
                ->url(fn (): string => route('podcasts.show', $this->record))
                ->openUrlInNewTab(),
        ];
    }
} 