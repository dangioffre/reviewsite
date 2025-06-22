<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Product;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\Hardware;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationLabel = 'Games';
    protected static ?string $navigationGroup = 'Games Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'games';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Game Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Basic Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                if ($operation !== 'create') {
                                                    return;
                                                }
                                                $set('slug', \Illuminate\Support\Str::slug($state));
                                            }),
                                        
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Product::class, 'slug', ignoreRecord: true)
                                            ->rules(['alpha_dash']),
                                        
                                        Forms\Components\Select::make('genres')
                                            ->label('Genres')
                                            ->multiple()
                                            ->options(Genre::active()->pluck('name', 'name'))
                                            ->searchable()
                                            ->helperText('Select multiple genres that apply to this game'),
                                        
                                        Forms\Components\Select::make('platforms')
                                            ->label('Platforms')
                                            ->multiple()
                                            ->options(Platform::active()->pluck('name', 'name'))
                                            ->searchable()
                                            ->helperText('Select all platforms this game is available on'),
                                        
                                        Forms\Components\DatePicker::make('release_date')
                                            ->label('Release Date'),
                                        
                                        Forms\Components\Select::make('theme_ids')
                                            ->label('Themes')
                                            ->multiple()
                                            ->relationship('themes', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select themes that apply to this game'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Development Information')
                                    ->schema([
                                        Forms\Components\Select::make('developer_ids')
                                            ->label('Developers')
                                            ->multiple()
                                            ->relationship('developers', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select all developers involved in this game'),
                                        
                                        Forms\Components\Select::make('publisher_ids')
                                            ->label('Publishers')
                                            ->multiple()
                                            ->relationship('publishers', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select all publishers involved in this game'),
                                        
                                        Forms\Components\Select::make('game_mode_ids')
                                            ->label('Game Modes')
                                            ->multiple()
                                            ->relationship('gameModes', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select game modes (e.g., Single-player, Multiplayer, Co-op, etc.)'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Media')
                            ->icon('heroicon-m-photo')
                            ->schema([
                                Forms\Components\Section::make('Main Media')
                                    ->schema([
                                        Forms\Components\TextInput::make('image')
                                            ->label('Main Game Image')
                                            ->url()
                                            ->helperText('Primary image displayed on game pages (preferably 16:9 aspect ratio)')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\TextInput::make('video_url')
                                            ->label('Main Gameplay Video')
                                            ->url()
                                            ->helperText('YouTube embed URL (e.g., https://www.youtube.com/embed/VIDEO_ID)')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Additional Photos')
                                    ->schema([
                                        Forms\Components\Repeater::make('photos')
                                            ->label('Game Screenshots & Photos')
                                            ->schema([
                                                Forms\Components\TextInput::make('url')
                                                    ->label('Image URL')
                                                    ->url()
                                                    ->required(),
                                                Forms\Components\TextInput::make('caption')
                                                    ->label('Caption')
                                                    ->maxLength(255)
                                                    ->helperText('Optional description for this image'),
                                                Forms\Components\Select::make('type')
                                                    ->label('Image Type')
                                                    ->options([
                                                        'screenshot' => 'Screenshot',
                                                        'artwork' => 'Artwork',
                                                        'poster' => 'Poster',
                                                        'other' => 'Other',
                                                    ])
                                                    ->default('screenshot'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Photo')
                                            ->helperText('Add multiple game images, screenshots, artwork, etc.')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Additional Videos')
                                    ->schema([
                                        Forms\Components\Repeater::make('videos')
                                            ->label('Additional Videos')
                                            ->schema([
                                                Forms\Components\TextInput::make('url')
                                                    ->label('Video URL')
                                                    ->url()
                                                    ->required()
                                                    ->helperText('YouTube embed URL'),
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Video Title')
                                                    ->maxLength(255)
                                                    ->required(),
                                                Forms\Components\Select::make('type')
                                                    ->label('Video Type')
                                                    ->options([
                                                        'gameplay' => 'Gameplay',
                                                        'trailer' => 'Trailer',
                                                        'review' => 'Review',
                                                        'walkthrough' => 'Walkthrough',
                                                        'other' => 'Other',
                                                    ])
                                                    ->default('gameplay'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Video')
                                            ->helperText('Add trailers, gameplay videos, reviews, etc.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Content')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\Section::make('Description')
                                    ->schema([
                                        Forms\Components\Textarea::make('description')
                                            ->label('Game Description')
                                            ->rows(4)
                                            ->helperText('Brief overview of the game (2-3 sentences)')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Story')
                                    ->schema([
                                        Forms\Components\RichEditor::make('story')
                                            ->label('Game Story')
                                            ->helperText('Detailed story/plot description of the game')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
                
                Forms\Components\Hidden::make('type')
                    ->default('game'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->size(60),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('genres')
                    ->badge()
                    ->separator(',')
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('platforms')
                    ->badge()
                    ->separator(',')
                    ->color('success')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('developers.name')
                    ->label('Developers')
                    ->badge()
                    ->separator(',')
                    ->color('warning')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('publishers.name')
                    ->label('Publishers')
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('themes.name')
                    ->label('Themes')
                    ->badge()
                    ->separator(',')
                    ->color('gray')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('gameModes.name')
                    ->label('Game Modes')
                    ->badge()
                    ->separator(',')
                    ->color('secondary')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('release_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('genre')
                    ->relationship('genre', 'name'),
                
                Tables\Filters\SelectFilter::make('platform')
                    ->relationship('platform', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'game');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
