<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HardwareResource\Pages;
use App\Filament\Resources\HardwareResource\RelationManagers;
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

class HardwareResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    
    protected static ?string $navigationLabel = 'Hardware';
    
    protected static ?string $navigationGroup = 'Products';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $slug = 'hardware';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Product Information')
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
                                        
                                        Forms\Components\Select::make('type')
                                            ->required()
                                            ->options([
                                                'hardware' => 'Hardware',
                                                'accessory' => 'Accessory',
                                            ])
                                            ->default('hardware')
                                            ->live(),
                                        
                                        Forms\Components\Select::make('genre_id')
                                            ->label('Primary Category')
                                            ->relationship('genre', 'name', fn ($query) => $query->where('type', 'hardware'))
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->helperText('Optional: Select a hardware category'),
                                        
                                        Forms\Components\Select::make('platform_id')
                                            ->label('Platform Compatibility')
                                            ->relationship('platform', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->helperText('Optional: Select primary platform compatibility'),
                                        
                                        Forms\Components\DatePicker::make('release_date')
                                            ->label('Release Date'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Manufacturer Information')
                                    ->schema([
                                        Forms\Components\Select::make('developer_ids')
                                            ->label('Manufacturers/Brands')
                                            ->multiple()
                                            ->relationship('developers', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select all manufacturers/brands involved'),
                                        
                                        Forms\Components\Select::make('game_mode_ids')
                                            ->label('Key Features')
                                            ->multiple()
                                            ->relationship('gameModes', 'name', fn ($query) => $query->where('type', 'hardware'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select key features (e.g., RGB Lighting, Wireless, Mechanical, etc.)'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Media')
                            ->icon('heroicon-m-photo')
                            ->schema([
                                Forms\Components\Section::make('Main Media')
                                    ->schema([
                                        Forms\Components\TextInput::make('image')
                                            ->label('Main Product Image')
                                            ->url()
                                            ->helperText('Primary image displayed on product pages')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\TextInput::make('video_url')
                                            ->label('Main Product Video')
                                            ->url()
                                            ->helperText('YouTube embed URL for product demonstration')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Additional Photos')
                                    ->schema([
                                        Forms\Components\Repeater::make('photos')
                                            ->label('Product Photos')
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
                                                        'product' => 'Product Photo',
                                                        'detail' => 'Detail Shot',
                                                        'packaging' => 'Packaging',
                                                        'usage' => 'In Use',
                                                        'other' => 'Other',
                                                    ])
                                                    ->default('product'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Photo')
                                            ->helperText('Add multiple product images, detail shots, etc.')
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
                                                        'demo' => 'Product Demo',
                                                        'unboxing' => 'Unboxing',
                                                        'review' => 'Review',
                                                        'tutorial' => 'Tutorial',
                                                        'other' => 'Other',
                                                    ])
                                                    ->default('demo'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Video')
                                            ->helperText('Add product demos, unboxing videos, reviews, etc.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Content')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\Section::make('Description')
                                    ->schema([
                                        Forms\Components\Textarea::make('description')
                                            ->label('Product Description')
                                            ->rows(4)
                                            ->helperText('Brief overview of the product (2-3 sentences)')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Detailed Information')
                                    ->schema([
                                        Forms\Components\RichEditor::make('story')
                                            ->label('Detailed Information')
                                            ->helperText('Detailed technical specifications, features, or additional information')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
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
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(function ($state) {
                        return match($state) {
                            'hardware' => 'warning',
                            'accessory' => 'info',
                            default => 'gray',
                        };
                    }),
                
                Tables\Columns\TextColumn::make('genre.name')
                    ->label('Category')
                    ->badge()
                    ->color('primary')
                    ->placeholder('N/A')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('platform.name')
                    ->label('Platform')
                    ->badge()
                    ->color('success')
                    ->placeholder('N/A')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('developers.name')
                    ->label('Brands')
                    ->badge()
                    ->separator(',')
                    ->color('warning')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('gameModes.name')
                    ->label('Key Features')
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
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'hardware' => 'Hardware',
                        'accessory' => 'Accessory',
                    ]),
                
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
        return parent::getEloquentQuery()->whereIn('type', ['hardware', 'accessory']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHardware::route('/'),
            'create' => Pages\CreateHardware::route('/create'),
            'edit' => Pages\EditHardware::route('/{record}/edit'),
        ];
    }
}
