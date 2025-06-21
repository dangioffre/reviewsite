<?php

namespace App\Filament\Resources\TechProductResource\Pages;

use App\Filament\Resources\TechProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTechProducts extends ListRecords
{
    protected static string $resource = TechProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
