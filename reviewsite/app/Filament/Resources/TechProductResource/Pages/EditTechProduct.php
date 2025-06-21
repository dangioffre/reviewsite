<?php

namespace App\Filament\Resources\TechProductResource\Pages;

use App\Filament\Resources\TechProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTechProduct extends EditRecord
{
    protected static string $resource = TechProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
