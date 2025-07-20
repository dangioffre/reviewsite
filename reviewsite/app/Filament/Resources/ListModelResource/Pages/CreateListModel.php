<?php

namespace App\Filament\Resources\ListModelResource\Pages;

use App\Filament\Resources\ListModelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateListModel extends CreateRecord
{
    protected static string $resource = ListModelResource::class;
}
