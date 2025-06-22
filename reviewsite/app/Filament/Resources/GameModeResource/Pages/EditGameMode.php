<?php

namespace App\Filament\Resources\GameModeResource\Pages;

use App\Filament\Resources\GameModeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameMode extends EditRecord
{
    protected static string $resource = GameModeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
