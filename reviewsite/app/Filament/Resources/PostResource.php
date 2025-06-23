<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Post Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'Article' => 'Article',
                                'Review' => 'Review',
                                'News' => 'News',
                            ])
                            ->required()
                            ->default('Article'),
                        Forms\Components\TextInput::make('author')
                            ->default('Dan & Brian')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Brief summary of the post (max 500 characters)'),
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ]),
                    ]),

                Forms\Components\Section::make('Media & Settings')
                    ->schema([
                        Forms\Components\TextInput::make('featured_image')
                            ->url()
                            ->label('Featured Image URL')
                            ->helperText('Enter a URL for the featured image'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Post')
                            ->helperText('Featured posts appear in the homepage slider')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('author')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'Article',
                        'success' => 'Review',
                        'warning' => 'News',
                    ]),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'Article' => 'Article',
                        'Review' => 'Review',
                        'News' => 'News',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Posts'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
