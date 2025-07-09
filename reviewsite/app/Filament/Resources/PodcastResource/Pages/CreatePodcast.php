<?php

namespace App\Filament\Resources\PodcastResource\Pages;

use App\Filament\Resources\PodcastResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePodcast extends CreateRecord
{
    protected static string $resource = PodcastResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate verification token if not provided
        if (empty($data['verification_token'])) {
            $data['verification_token'] = 'verify-' . uniqid() . '-' . time();
        }

        return $data;
    }
} 