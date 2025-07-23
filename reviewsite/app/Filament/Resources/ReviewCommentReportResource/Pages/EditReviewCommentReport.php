<?php

namespace App\Filament\Resources\ReviewCommentReportResource\Pages;

use App\Filament\Resources\ReviewCommentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditReviewCommentReport extends EditRecord
{
    protected static string $resource = ReviewCommentReportResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\DeleteAction::make(),
            Actions\Action::make('delete_comment')
                ->label('Delete Comment')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Comment')
                ->modalDescription('Are you sure you want to permanently delete this comment? This action cannot be undone.')
                ->action(function () {
                    $record = $this->record;
                    if ($record->comment) {
                        $record->comment->delete();
                        $record->resolved = true;
                        $record->save();
                        Notification::make()->success()->title('Comment deleted and report resolved.')->send();
                        return redirect()->route('filament.admin.resources.review-comment-reports.index');
                    }
                }),
            Actions\Action::make('its_fine')
                ->label("It's Fine")
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading("Mark as Fine")
                ->modalDescription('This will mark the report as resolved and keep the comment.')
                ->action(function () {
                    $record = $this->record;
                    $record->resolved = true;
                    $record->save();
                    Notification::make()->success()->title('Report marked as resolved.')->send();
                    return redirect()->route('filament.admin.resources.review-comment-reports.index');
                }),
        ];
        return $actions;
    }
}
