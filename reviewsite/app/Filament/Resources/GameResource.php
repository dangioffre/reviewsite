<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Product;
use App\Models\Genre;
use App\Models\Platform;

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
    protected static ?string $navigationGroup = 'Products';
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
                                // Core Game Information
                                Forms\Components\Section::make('Core Game Information')
                                    ->description('Essential details about the game')
                                    ->icon('heroicon-m-star')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Game Title')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Enter the full game title')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                if ($operation !== 'create') {
                                                    return;
                                                }
                                                $set('slug', \Illuminate\Support\Str::slug($state));
                                            }),
                                        
                                        Forms\Components\TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Product::class, 'slug', ignoreRecord: true)
                                            ->rules(['alpha_dash'])
                                            ->placeholder('auto-generated-from-title')
                                            ->helperText('This will be used in the game\'s URL'),
                                        
                                        Forms\Components\DatePicker::make('release_date')
                                            ->label('Release Date')
                                            ->placeholder('Select release date')
                                            ->helperText('When the game was or will be released'),
                                        
                                        Forms\Components\Select::make('platform_id')
                                            ->label('Primary Platform')
                                            ->relationship('platform', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select primary platform')
                                            ->helperText('The main platform this game is primarily associated with')
                                            ->required(),
                                        
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Featured Game')
                                            ->helperText('Featured games appear prominently on the homepage')
                                            ->default(false)
                                            ->onIcon('heroicon-s-star')
                                            ->offIcon('heroicon-s-star')
                                            ->onColor('warning')
                                            ->offColor('gray'),
                                    ])
                                    ->columns(2),

                                // Classification & Ratings
                                Forms\Components\Section::make('Classification & Ratings')
                                    ->description('Game categorization and age ratings')
                                    ->icon('heroicon-m-shield-check')
                                    ->schema([
                                        Forms\Components\Select::make('genre_id')
                                            ->label('Primary Genre')
                                            ->relationship('genre', 'name', fn ($query) => $query->where('type', 'game'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select primary genre')
                                            ->helperText('The main genre that best describes this game'),
                                        
                                        Forms\Components\Select::make('platform_id')
                                            ->label('Primary Platform')
                                            ->relationship('platform', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select primary platform')
                                            ->helperText('The main platform this game is primarily associated with')
                                            ->required(),
                                        
                                        Forms\Components\Select::make('genre_ids')
                                            ->label('Additional Genres')
                                            ->multiple()
                                            ->relationship('genres', 'name', fn ($query) => $query->where('type', 'game'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select additional genres')
                                            ->helperText('Select all other genres that apply to this game'),
                                        
                                        Forms\Components\Select::make('platform_ids')
                                            ->label('Additional Platforms')
                                            ->multiple()
                                            ->relationship('platforms', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select additional platforms')
                                            ->helperText('Select all other platforms this game is available on'),
                                        
                                        Forms\Components\Select::make('esrb_rating_id')
                                            ->label('ESRB Rating')
                                            ->relationship('esrbRating', 'name', fn ($query) => $query->where('type', 'esrb'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select ESRB rating')
                                            ->helperText('ESRB age rating for North American markets'),
                                        
                                        Forms\Components\Select::make('pegi_rating_id')
                                            ->label('PEGI Rating')
                                            ->relationship('pegiRating', 'name', fn ($query) => $query->where('type', 'pegi'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select PEGI rating')
                                            ->helperText('PEGI age rating for European markets'),
                                    ])
                                    ->columns(2),

                                // Game Characteristics
                                Forms\Components\Section::make('Game Characteristics')
                                    ->description('Gameplay features and themes')
                                    ->icon('heroicon-m-puzzle-piece')
                                    ->schema([
                                        Forms\Components\Select::make('theme_ids')
                                            ->label('Themes')
                                            ->multiple()
                                            ->relationship('themes', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select themes')
                                            ->helperText('Select all themes that apply to this game'),
                                        
                                        Forms\Components\Select::make('keyword_ids')
                                            ->label('Keywords')
                                            ->multiple()
                                            ->relationship('keywords', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Keyword Name')
                                                    ->required()
                                                    ->maxLength(255),
                                            ])
                                            ->helperText('Add or select keywords to help categorize and describe this game'),
                                        
                                        Forms\Components\Select::make('player_perspective_ids')
                                            ->label('Player Perspectives')
                                            ->multiple()
                                            ->relationship('playerPerspectives', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select perspectives')
                                            ->helperText('Select all player perspectives available in this game'),
                                        
                                        Forms\Components\Select::make('game_mode_ids')
                                            ->label('Game Modes')
                                            ->multiple()
                                            ->relationship('gameModes', 'name', fn ($query) => $query->where('type', 'game'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select game modes')
                                            ->helperText('Select all available game modes (Single-player, Multiplayer, Co-op, etc.)'),
                                    ])
                                    ->columns(3),

                                // Development Team
                                Forms\Components\Section::make('Development Team')
                                    ->description('Companies and teams involved in development')
                                    ->icon('heroicon-m-building-office')
                                    ->schema([
                                        Forms\Components\Select::make('developer_ids')
                                            ->label('Developers')
                                            ->multiple()
                                            ->relationship('developers', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select developers')
                                            ->helperText('Select all development studios involved in this game'),
                                        
                                        Forms\Components\Select::make('publisher_ids')
                                            ->label('Publishers')
                                            ->multiple()
                                            ->relationship('publishers', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select publishers')
                                            ->helperText('Select all publishing companies involved in this game'),
                                    ])
                                    ->columns(2),

                                // External Links
                                Forms\Components\Section::make('External Links')
                                    ->description('Official websites and resources')
                                    ->icon('heroicon-m-link')
                                    ->schema([
                                        Forms\Components\TextInput::make('official_website')
                                            ->label('Official Website')
                                            ->url()
                                            ->placeholder('https://example.com')
                                            ->helperText('Link to the official game website or store page'),
                                    ])
                                    ->columns(1),
                            ]),

                        Forms\Components\Tabs\Tab::make('Media')
                            ->icon('heroicon-m-photo')
                            ->schema([
                                // Primary Media Assets
                                Forms\Components\Section::make('Primary Media Assets')
                                    ->description('Main promotional and display media')
                                    ->icon('heroicon-m-star')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Main Game Image')
                                            ->directory('uploads/games')
                                            ->image()
                                            ->imagePreviewHeight('180')
                                            ->columnSpanFull()
                                            ->helperText('Upload or select a main promotional image (recommended: 1920x1080, 16:9 aspect ratio)'),
                                        Forms\Components\TextInput::make('image_url')
                                            ->label('Alternate Image URL')
                                            ->url()
                                            ->placeholder('https://example.com/game-image.jpg')
                                            ->helperText('Or provide a URL for the main promotional image')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('video_url')
                                            ->label('Featured Video')
                                            ->url()
                                            ->placeholder('https://www.youtube.com/embed/VIDEO_ID')
                                            ->helperText('Main gameplay trailer or promotional video (YouTube embed URL)')
                                            ->columnSpanFull(),
                                    ]),

                                // Game Screenshots & Artwork
                                Forms\Components\Section::make('Game Screenshots & Artwork')
                                    ->description('In-game screenshots, concept art, and promotional images')
                                    ->icon('heroicon-m-photo')
                                    ->schema([
                                        Forms\Components\Repeater::make('photos')
                                            ->label('Images')
                                            ->schema([
                                                Forms\Components\FileUpload::make('upload')
                                                    ->label('Upload Image')
                                                    ->directory('uploads/games')
                                                    ->image()
                                                    ->imagePreviewHeight('120'),
                                                Forms\Components\TextInput::make('url')
                                                    ->label('Image URL')
                                                    ->url()
                                                    ->placeholder('https://example.com/image.jpg'),
                                                Forms\Components\TextInput::make('caption')
                                                    ->label('Caption')
                                                    ->maxLength(255)
                                                    ->placeholder('Brief description of this image')
                                                    ->helperText('Optional description for this image'),
                                                Forms\Components\Select::make('type')
                                                    ->label('Image Type')
                                                    ->options([
                                                        'screenshot' => 'Screenshot',
                                                        'artwork' => 'Artwork',
                                                        'poster' => 'Poster',
                                                        'concept' => 'Concept Art',
                                                        'other' => 'Other',
                                                    ])
                                                    ->default('screenshot')
                                                    ->helperText('Category for this image'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Image')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['caption'] ?? $state['url'] ?? null)
                                            ->helperText('Add multiple game images, screenshots, artwork, etc.')
                                            ->columnSpanFull(),
                                    ]),

                                // Video Content
                                Forms\Components\Section::make('Video Content')
                                    ->description('Trailers, gameplay videos, reviews, and walkthroughs')
                                    ->icon('heroicon-m-play')
                                    ->schema([
                                        Forms\Components\Repeater::make('videos')
                                            ->label('Videos')
                                            ->schema([
                                                Forms\Components\TextInput::make('url')
                                                    ->label('Video URL')
                                                    ->url()
                                                    ->required()
                                                    ->placeholder('https://www.youtube.com/embed/VIDEO_ID')
                                                    ->helperText('YouTube embed URL (not regular YouTube URL)'),
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Video Title')
                                                    ->maxLength(255)
                                                    ->required()
                                                    ->placeholder('Enter video title'),
                                                Forms\Components\Select::make('type')
                                                    ->label('Video Type')
                                                    ->options([
                                                        'gameplay' => 'Gameplay',
                                                        'trailer' => 'Trailer',
                                                        'review' => 'Review',
                                                        'walkthrough' => 'Walkthrough',
                                                        'interview' => 'Developer Interview',
                                                        'other' => 'Other',
                                                    ])
                                                    ->default('gameplay')
                                                    ->helperText('Category for this video'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Video')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? $state['url'] ?? null)
                                            ->helperText('Add trailers, gameplay videos, reviews, developer interviews, etc.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Content')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                // Game Overview
                                Forms\Components\Section::make('Game Overview')
                                    ->description('Brief description and summary')
                                    ->icon('heroicon-m-information-circle')
                                    ->schema([
                                        Forms\Components\Textarea::make('description')
                                            ->label('Game Description')
                                            ->rows(4)
                                            ->placeholder('Enter a brief overview of the game...')
                                            ->helperText('A concise summary of the game (2-3 sentences) that will appear in listings and previews')
                                            ->columnSpanFull(),
                                    ]),

                                // Story & Narrative
                                Forms\Components\Section::make('Story & Narrative')
                                    ->description('Detailed story, plot, and narrative content')
                                    ->icon('heroicon-m-book-open')
                                    ->schema([
                                        Forms\Components\RichEditor::make('story')
                                            ->label('Game Story')
                                            ->placeholder('Enter the game\'s story, plot, and narrative details...')
                                            ->helperText('Detailed story/plot description, character information, and narrative elements')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                                'h2',
                                                'h3',
                                                'blockquote',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Affiliate Links')
                            ->icon('heroicon-m-shopping-cart')
                            ->schema([
                                // Affiliate Links Management
                                Forms\Components\Section::make('Purchase Links')
                                    ->description('Add affiliate links for purchasing the game and DLC on various platforms')
                                    ->icon('heroicon-m-currency-dollar')
                                    ->schema([
                                        Forms\Components\Repeater::make('affiliate_links')
                                            ->label('Affiliate Links')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Link Title')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('e.g., Buy on Steam, Amazon, etc.')
                                                    ->helperText('Display name for this affiliate link'),
                                                
                                                Forms\Components\TextInput::make('url')
                                                    ->label('Affiliate URL')
                                                    ->url()
                                                    ->required()
                                                    ->placeholder('https://store.steampowered.com/app/...')
                                                    ->helperText('The affiliate link URL'),
                                                
                                                Forms\Components\Select::make('type')
                                                    ->label('Link Type')
                                                    ->options([
                                                        'main_game' => 'Main Game',
                                                        'dlc' => 'DLC/Expansion',
                                                        'season_pass' => 'Season Pass',
                                                        'collectors_edition' => 'Collector\'s Edition',
                                                        'digital_deluxe' => 'Digital Deluxe',
                                                        'physical' => 'Physical Copy',
                                                        'other' => 'Other',
                                                    ])
                                                    ->default('main_game')
                                                    ->required()
                                                    ->helperText('Category for this affiliate link'),
                                                
                                                Forms\Components\TextInput::make('price')
                                                    ->label('Price (Optional)')
                                                    ->maxLength(50)
                                                    ->placeholder('$59.99, €49.99, etc.')
                                                    ->helperText('Current price (will be displayed if provided)'),
                                                
                                                Forms\Components\TextInput::make('platform')
                                                    ->label('Platform (Optional)')
                                                    ->maxLength(100)
                                                    ->placeholder('PC, PS5, Xbox, Switch')
                                                    ->helperText('Specific platform for this link'),
                                                
                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Active')
                                                    ->default(true)
                                                    ->helperText('Whether this link should be displayed'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Affiliate Link')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New Affiliate Link')
                                            ->helperText('Add affiliate links for purchasing the game and DLC on various platforms')
                                            ->columnSpanFull(),
                                    ]),

                                // Affiliate Links Help
                                Forms\Components\Section::make('Affiliate Links Guide')
                                    ->description('Tips for creating effective affiliate links')
                                    ->icon('heroicon-m-question-mark-circle')
                                    ->schema([
                                        Forms\Components\Placeholder::make('affiliate_help')
                                            ->content('
                                                <div class="space-y-4">
                                                    <div>
                                                        <h4 class="font-semibold text-gray-900 dark:text-white">Link Types:</h4>
                                                        <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 mt-2">
                                                            <li><strong>Main Game:</strong> Primary game purchase links (Steam, PlayStation Store, etc.)</li>
                                                            <li><strong>DLC/Expansion:</strong> Downloadable content and expansion packs</li>
                                                            <li><strong>Season Pass:</strong> Season passes and content bundles</li>
                                                            <li><strong>Collector\'s Edition:</strong> Special collector\'s editions and physical copies</li>
                                                            <li><strong>Digital Deluxe:</strong> Digital deluxe editions with bonus content</li>
                                                            <li><strong>Physical Copy:</strong> Physical retail copies</li>
                                                        </ul>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-gray-900 dark:text-white">Best Practices:</h4>
                                                        <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 mt-2">
                                                            <li>Use descriptive titles (e.g., "Buy on Steam" instead of just "Steam")</li>
                                                            <li>Include current prices when available</li>
                                                            <li>Specify the platform for each link</li>
                                                            <li>Keep links active and up-to-date</li>
                                                            <li>Use proper affiliate tracking URLs</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            ')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsed(),
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
                
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Featured')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('genre.name')
                    ->label('Genre')
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('platform.name')
                    ->label('Platform')
                    ->badge()
                    ->color('success')
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
                
                Tables\Filters\SelectFilter::make('is_featured')
                    ->label('Featured Status')
                    ->options([
                        true => 'Featured',
                        false => 'Not Featured',
                    ])
                    ->placeholder('All Games'),
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

    public static function canAccessResource(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('Admin') || $user->hasRole('Moderator'));
    }
}
