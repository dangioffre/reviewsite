<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    
    protected static ?string $navigationGroup = 'Moderation';
    
    protected static ?string $navigationLabel = 'Review Reports';
    
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Details')
                    ->schema([
                        Forms\Components\Select::make('review_id')
                            ->relationship('review', 'title')
                            ->searchable()
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('reason')
                            ->options(Report::getReasons())
                            ->required()
                            ->disabled(),
                        Forms\Components\Textarea::make('additional_info')
                            ->label('Additional Information')
                            ->rows(3)
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Resolution')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved (Review Deleted)',
                                'denied' => 'Denied (Review Kept)',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->helperText('Internal notes about the resolution'),
                        Forms\Components\Select::make('resolved_by')
                            ->relationship('resolvedBy', 'name')
                            ->searchable()
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->disabled(),
                    ])->columns(2),
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
                Tables\Columns\TextColumn::make('review.title')
                    ->label('Reported Review')
                    ->limit(50)
                    ->searchable()
                    ->url(fn ($record) => $record->review ? 
                        route($record->review->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', 
                              [$record->review->product, $record->review]) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('review.user.name')
                    ->label('Review Author')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Reported By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->formatStateUsing(fn (string $state): string => Report::getReasons()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'inappropriate' => 'danger',
                        'spam' => 'warning',
                        'offensive' => 'danger',
                        'fake' => 'warning',
                        'duplicate' => 'info',
                        'other' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'denied' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending Review',
                        'approved' => 'Approved (Deleted)',
                        'denied' => 'Denied (Kept)',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reported At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Resolved At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('resolvedBy.name')
                    ->label('Resolved By')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'denied' => 'Denied',
                    ]),
                Tables\Filters\SelectFilter::make('reason')
                    ->options(Report::getReasons()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('approve')
                    ->label('Approve & Delete Review')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Report $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Report')
                    ->modalDescription('This will delete the reported review permanently. Are you sure?')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->helperText('Optional notes about this decision'),
                    ])
                    ->action(function (Report $record, array $data): void {
                        $record->approve(Auth::id(), $data['admin_notes'] ?? null);
                    })
                    ->successNotificationTitle('Report approved and review deleted'),
                
                Action::make('deny')
                    ->label('Deny & Keep Review')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Report $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Deny Report')
                    ->modalDescription('This will keep the review and mark the report as denied.')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->helperText('Optional notes about this decision'),
                    ])
                    ->action(function (Report $record, array $data): void {
                        $record->deny(Auth::id(), $data['admin_notes'] ?? null);
                    })
                    ->successNotificationTitle('Report denied and review kept'),
                
                Tables\Actions\EditAction::make()
                    ->visible(fn (Report $record): bool => $record->status === 'pending'),
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
            'index' => Pages\ListReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::pending()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = static::getModel()::pending()->count();
        return $pendingCount > 0 ? 'warning' : null;
    }
}
