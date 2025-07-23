<?php

namespace App\Filament\Resources\GameResource\Pages;

use App\Filament\Resources\GameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\OpenAIService;
use Filament\Notifications\Notification;

class EditGame extends EditRecord
{
    protected static string $resource = GameResource::class;

    public function mount($record): void
    {
        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('generateDescription')
                ->label('AI Generate Description')
                ->action('generateDescription'),
            Actions\Action::make('generateStory')
                ->label('AI Generate Story')
                ->action('generateStory'),
            Actions\Action::make('generateKeywords')
                ->label('AI Generate Keywords')
                ->action('generateKeywords'),
        ];
    }

    public function generateDescription()
    {
        try {
            $openAIService = app(OpenAIService::class);
            $gameName = $this->form->getState()['name'] ?? $this->data['name'] ?? $this->record->name;
            $genre = $this->form->getState()['genre_id'] ? optional($this->record->genre)->name : null;
            $description = $openAIService->generateDescription($gameName, $genre);
            $this->form->fill(['description' => $description]);
            Notification::make()
                ->title('AI-generated description added!')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('AI generation failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function generateStory()
    {
        try {
            $openAIService = app(OpenAIService::class);
            $gameName = $this->form->getState()['name'] ?? $this->data['name'] ?? $this->record->name;
            $genre = $this->form->getState()['genre_id'] ? optional($this->record->genre)->name : null;
            $story = $openAIService->generateStory($gameName, $genre);
            $this->form->fill(['story' => $story]);
            Notification::make()
                ->title('AI-generated story added!')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('AI generation failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function generateKeywords()
    {
        try {
            $openAIService = app(OpenAIService::class);
            $gameName = $this->form->getState()['name'] ?? $this->data['name'] ?? $this->record->name;
            $genre = $this->form->getState()['genre_id'] ? optional($this->record->genre)->name : null;
            $keywords = $openAIService->generateKeywords($gameName, $genre);
            // Find or create keywords and get their IDs
            $keywordIds = collect($keywords)->map(function ($keyword) {
                $model = \App\Models\Keyword::firstOrCreate([
                    'name' => $keyword
                ], [
                    'slug' => \Illuminate\Support\Str::slug($keyword)
                ]);
                return $model->id;
            })->toArray();
            $this->form->fill(['keyword_ids' => $keywordIds]);
            Notification::make()
                ->title('AI-generated keywords added!')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('AI generation failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
