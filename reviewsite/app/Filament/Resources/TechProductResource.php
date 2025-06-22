<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechProductResource\Pages;
use App\Filament\Resources\TechProductResource\RelationManagers;
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

class TechProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    
    protected static ?string $navigationLabel = 'Tech Products';
    
    protected static ?string $navigationGroup = 'Tech Management';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $slug = 'tech-products';

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
                                            ->live(),
                                        
                                        Forms\Components\Select::make('genre_id')
                                            ->label('Category')
                                            ->options(Genre::active()->pluck('name', 'id'))
                                            ->searchable()
                                            ->helperText('Use genres to categorize your tech products'),
                                        
                                        Forms\Components\Select::make('platform_id')
                                            ->label('Platform Compatibility')
                                            ->options(Platform::active()->pluck('name', 'id'))
                                            ->searchable()
                                            ->helperText('Which platform is this compatible with?'),
                                        
                                        Forms\Components\Select::make('hardware_id')
                                            ->label('Hardware (for Accessories)')
                                            ->options(Hardware::active()->pluck('name', 'id'))
                                            ->searchable()
                                            ->visible(fn (Forms\Get $get) => $get('type') === 'accessory')
                                            ->helperText('Select the hardware this accessory is compatible with'),
                                        
                                        Forms\Components\DatePicker::make('release_date')
                                            ->label('Release Date'),
                                        
                                        Forms\Components\TextInput::make('theme')
                                            ->label('Theme/Category')
                                            ->maxLength(255)
                                            ->helperText('e.g., Gaming, Professional, Budget, Premium, etc.'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Manufacturer Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('developer')
                                            ->label('Manufacturer/Brand')
                                            ->maxLength(255),
                                        
                                        Forms\Components\TextInput::make('publisher')
                                            ->label('Publisher/Distributor')
                                            ->maxLength(255),
                                        
                                        Forms\Components\TextInput::make('game_modes')
                                            ->label('Key Features')
                                            ->maxLength(255)
                                            ->helperText('e.g., RGB Lighting, Wireless, Mechanical, etc.'),
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
                    ->color(fn ($record) => $record->genre?->color ?: 'gray'),
                
                Tables\Columns\TextColumn::make('platform.name')
                    ->badge()
                    ->color(fn ($record) => $record->platform?->color ?: 'gray'),
                
                Tables\Columns\TextColumn::make('hardware.name')
                    ->badge()
                    ->color(fn ($record) => $record->hardware?->color ?: 'gray')
                    ->placeholder('N/A'),
                
                Tables\Columns\TextColumn::make('developer')
                    ->label('Brand')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('publisher')
                    ->label('Publisher')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('theme')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                
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
                    ->relationship('genre', 'name')
                    ->label('Category'),
                
                Tables\Filters\SelectFilter::make('platform')
                    ->relationship('platform', 'name'),
                
                Tables\Filters\SelectFilter::make('hardware')
                    ->relationship('hardware', 'name'),
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
            'index' => Pages\ListTechProducts::route('/'),
            'create' => Pages\CreateTechProduct::route('/create'),
            'edit' => Pages\EditTechProduct::route('/{record}/edit'),
        ];
    }
}
