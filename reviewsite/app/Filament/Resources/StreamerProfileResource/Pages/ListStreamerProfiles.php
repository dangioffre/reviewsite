<?php

namespace App\Filament\Resources\StreamerProfileResource\Pages;

use App\Filament\Resources\StreamerProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStreamerProfiles extends ListRecords
{
    protected static string $resource = StreamerProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}