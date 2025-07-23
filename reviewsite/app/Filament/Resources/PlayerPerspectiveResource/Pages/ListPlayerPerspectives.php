<?php

namespace App\Filament\Resources\PlayerPerspectiveResource\Pages;

use App\Filament\Resources\PlayerPerspectiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlayerPerspectives extends ListRecords
{
    protected static string $resource = PlayerPerspectiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 