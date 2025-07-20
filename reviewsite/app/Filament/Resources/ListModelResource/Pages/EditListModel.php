<?php

namespace App\Filament\Resources\ListModelResource\Pages;

use App\Filament\Resources\ListModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListModel extends EditRecord
{
    protected static string $resource = ListModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
