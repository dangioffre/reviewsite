<?php

namespace App\Filament\Resources\GameTipResource\Pages;

use App\Filament\Resources\GameTipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameTip extends EditRecord
{
    protected static string $resource = GameTipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
