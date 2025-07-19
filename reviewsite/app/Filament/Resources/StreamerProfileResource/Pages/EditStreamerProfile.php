<?php

namespace App\Filament\Resources\StreamerProfileResource\Pages;

use App\Filament\Resources\StreamerProfileResource;
use App\Services\StreamerAuditService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStreamerProfile extends EditRecord
{
    protected static string $resource = StreamerProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    StreamerAuditService::logDeletion($this->record);
                }),
        ];
    }

    protected function afterSave(): void
    {
        $changes = [];
        
        // Track changes made during the edit
        if ($this->record->wasChanged()) {
            $changes = $this->record->getChanges();
            unset($changes['updated_at']); // Remove timestamp changes
        }

        if (!empty($changes)) {
            StreamerAuditService::logEdit($this->record, $changes);
        }
    }
}