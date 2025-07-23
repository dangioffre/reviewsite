<?php

namespace App\Filament\Resources\ReviewCommentReportResource\Pages;

use App\Filament\Resources\ReviewCommentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviewCommentReports extends ListRecords
{
    protected static string $resource = ReviewCommentReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
