<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Models\Episode;
use App\Models\Podcast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Episode Information')
                    ->schema([
                        Forms\Components\Select::make('podcast_id')
                            ->label('Podcast')
                            ->relationship('podcast', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $context, $state, callable $set) {
                                if ($context === 'create') {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('show_notes')
                            ->label('Show Notes')
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Published At')
                            ->required(),

                        Forms\Components\TextInput::make('audio_url')
                            ->label('Audio URL')
                            ->url()
                            ->maxLength(500),

                        Forms\Components\TextInput::make('artwork_url')
                            ->label('Artwork URL')
                            ->url()
                            ->maxLength(500),

                        Forms\Components\TextInput::make('duration')
                            ->label('Duration (seconds)')
                            ->numeric()
                            ->suffix('seconds'),

                        Forms\Components\TextInput::make('episode_number')
                            ->label('Episode Number')
                            ->numeric(),

                        Forms\Components\TextInput::make('season_number')
                            ->label('Season Number')
                            ->numeric(),

                        Forms\Components\Select::make('episode_type')
                            ->label('Episode Type')
                            ->options([
                                'full' => 'Full Episode',
                                'trailer' => 'Trailer',
                                'bonus' => 'Bonus',
                            ])
                            ->default('full'),

                        Forms\Components\Toggle::make('is_explicit')
                            ->label('Explicit Content'),

                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('Add tags...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('artwork_url')
                    ->label('Artwork')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/default-episode.png')),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->limit(50),

                Tables\Columns\TextColumn::make('podcast.name')
                    ->label('Podcast')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('episode_number')
                    ->label('Episode #')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('season_number')
                    ->label('Season #')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $minutes = floor($state / 60);
                        $seconds = $state % 60;
                        return sprintf('%d:%02d', $minutes, $seconds);
                    })
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('episode_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'full',
                        'warning' => 'trailer',
                        'success' => 'bonus',
                    ]),

                Tables\Columns\IconColumn::make('is_explicit')
                    ->label('Explicit')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Reviews')
                    ->counts('reviews')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('podcast_id')
                    ->label('Podcast')
                    ->relationship('podcast', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('episode_type')
                    ->label('Episode Type')
                    ->options([
                        'full' => 'Full Episode',
                        'trailer' => 'Trailer',
                        'bonus' => 'Bonus',
                    ]),

                Tables\Filters\TernaryFilter::make('is_explicit')
                    ->label('Explicit Content')
                    ->placeholder('All episodes')
                    ->trueLabel('Explicit')
                    ->falseLabel('Clean'),

                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->label('Published from'),
                        Forms\Components\DatePicker::make('published_until')
                            ->label('Published until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Episode Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('podcast.name')
                                    ->label('Podcast')
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('episode_number')
                                    ->label('Episode #'),

                                Infolists\Components\TextEntry::make('season_number')
                                    ->label('Season #'),

                                Infolists\Components\TextEntry::make('episode_type')
                                    ->label('Type')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'full' => 'primary',
                                        'trailer' => 'warning',
                                        'bonus' => 'success',
                                    }),

                                Infolists\Components\IconEntry::make('is_explicit')
                                    ->label('Explicit')
                                    ->boolean(),

                                Infolists\Components\TextEntry::make('duration')
                                    ->label('Duration')
                                    ->formatStateUsing(function ($state) {
                                        if (!$state) return '-';
                                        $minutes = floor($state / 60);
                                        $seconds = $state % 60;
                                        return sprintf('%d:%02d', $minutes, $seconds);
                                    }),

                                Infolists\Components\TextEntry::make('published_at')
                                    ->label('Published')
                                    ->dateTime(),

                                Infolists\Components\TextEntry::make('description')
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('audio_url')
                                    ->label('Audio URL')
                                    ->url()
                                    ->openUrlInNewTab()
                                    ->visible(fn ($record) => !empty($record->audio_url)),

                                Infolists\Components\TextEntry::make('artwork_url')
                                    ->label('Artwork URL')
                                    ->url()
                                    ->openUrlInNewTab()
                                    ->visible(fn ($record) => !empty($record->artwork_url)),

                                Infolists\Components\TextEntry::make('tags')
                                    ->badge()
                                    ->separator(',')
                                    ->visible(fn ($record) => !empty($record->tags)),
                            ]),
                    ]),

                Infolists\Components\Section::make('Show Notes')
                    ->schema([
                        Infolists\Components\TextEntry::make('show_notes')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->show_notes)),

                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('reviews_count')
                                    ->label('Reviews')
                                    ->numeric(),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime(),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Updated')
                                    ->dateTime(),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'view' => Pages\ViewEpisode::route('/{record}'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
} 