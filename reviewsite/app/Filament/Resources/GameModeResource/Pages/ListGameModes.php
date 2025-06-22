<?php

namespace App\Filament\Resources\GameModeResource\Pages;

use App\Filament\Resources\GameModeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameModes extends ListRecords
{
    protected static string $resource = GameModeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
