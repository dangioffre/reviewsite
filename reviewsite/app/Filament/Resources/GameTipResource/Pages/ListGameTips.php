<?php

namespace App\Filament\Resources\GameTipResource\Pages;

use App\Filament\Resources\GameTipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameTips extends ListRecords
{
    protected static string $resource = GameTipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
