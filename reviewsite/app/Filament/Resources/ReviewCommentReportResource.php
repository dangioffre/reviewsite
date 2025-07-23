<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewCommentReportResource\Pages;
use App\Filament\Resources\ReviewCommentReportResource\RelationManagers;
use App\Models\ReviewCommentReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ReviewCommentReportResource extends Resource
{
    protected static ?string $model = \App\Models\ReviewCommentReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Moderation';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Comment Reports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Details')
                    ->schema([
                        Forms\Components\TextInput::make('comment_info')
                            ->label('Reported Comment')
                            ->formatStateUsing(function ($record) {
                                if ($record && $record->comment) {
                                    return $record->comment->content;
                                }
                                return '';
                            })
                            ->disabled(),
                        // Comment Author
                        Forms\Components\Placeholder::make('comment_author')
                            ->label('Comment Author')
                            ->content(function ($record) {
                                if ($record && $record->comment && $record->comment->user) {
                                    return $record->comment->user->name;
                                }
                                return 'N/A';
                            }),
                        Forms\Components\Select::make('reason')
                            ->options([
                                'Spam' => 'Spam',
                                'Harassment' => 'Harassment',
                                'Off-topic' => 'Off-topic',
                                'Inappropriate' => 'Inappropriate',
                                'Other' => 'Other',
                            ])
                            ->required()
                            ->disabled(),
                        Forms\Components\Textarea::make('details')
                            ->label('Details')
                            ->rows(3)
                            ->disabled(),
                        Forms\Components\Placeholder::make('reported_by')
                            ->label('Reported By')
                            ->content(function ($record) {
                                if ($record && $record->user) {
                                    return $record->user->name;
                                }
                                return 'N/A';
                            }),
                        Forms\Components\Placeholder::make('review_page_link')
                            ->label('Review Page')
                            ->content(function ($record) {
                                if ($record && $record->comment && $record->comment->review && $record->comment->review->product) {
                                    $review = $record->comment->review;
                                    $product = $review->product;
                                    $route = route($product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$product, $review]);
                                    return new HtmlString('<a href="' . $route . '" target="_blank" class="text-blue-500 underline">View Review Page</a>');
                                }
                                return 'N/A';
                            }),
                    ])->columns(2),
                Forms\Components\Section::make('Status & Resolution')
                    ->schema([
                        Forms\Components\Toggle::make('resolved')
                            ->label('Resolved'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Report #')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('comment.content')
                    ->label('Comment')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Reported By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reason')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Inappropriate' => 'danger',
                        'Spam' => 'warning',
                        'Harassment' => 'danger',
                        'Off-topic' => 'info',
                        'Other' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('resolved')
                    ->boolean()
                    ->label('Resolved'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reported At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('resolved')
                    ->label('Resolved'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),
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
            'index' => Pages\ListReviewCommentReports::route('/'),
            'create' => Pages\CreateReviewCommentReport::route('/create'),
            'edit' => Pages\EditReviewCommentReport::route('/{record}/edit'),
        ];
    }
}
