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
    
    protected static ?string $navigationGroup = 'Community';
    
    protected static ?string $navigationLabel = 'Review Reports';
    
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Details')
                    ->schema([
                        Forms\Components\TextInput::make('review_info')
                            ->label('Reported Review')
                            ->formatStateUsing(function ($record) {
                                if ($record && $record->review) {
                                    return $record->review->title;
                                } else if ($record) {
                                    return ($record->review_title ?? 'Unknown Review') . ' (DELETED)';
                                }
                                return '';
                            })
                            ->disabled(),
                        Forms\Components\TextInput::make('product_info')
                            ->label('Product')
                            ->formatStateUsing(function ($record) {
                                if ($record && $record->review) {
                                    return $record->review->product->name . ' (' . ucfirst($record->review->product->type) . ')';
                                } else if ($record) {
                                    return ($record->product_name ?? 'Unknown Product') . ' (' . ucfirst($record->product_type ?? 'unknown') . ')';
                                }
                                return '';
                            })
                            ->disabled(),
                        Forms\Components\TextInput::make('review_author_info')
                            ->label('Review Author')
                            ->formatStateUsing(function ($record) {
                                if ($record && $record->review) {
                                    return $record->review->user->name;
                                } else if ($record) {
                                    return $record->review_author_name ?? 'Unknown Author';
                                }
                                return '';
                            })
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
                Tables\Columns\TextColumn::make('review_info')
                    ->label('Reported Review')
                    ->limit(50)
                    ->searchable(['review_title', 'product_name'])
                    ->formatStateUsing(function ($record) {
                        if ($record->review) {
                            // Review still exists
                            return $record->review->title;
                        } else {
                            // Review was deleted, show stored info
                            return ($record->review_title ?? 'Unknown Review') . ' (DELETED)';
                        }
                    })
                    ->url(function ($record) {
                        if ($record->review) {
                            return route($record->review->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', 
                                  [$record->review->product, $record->review]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab()
                    ->color(fn ($record) => $record->review ? 'primary' : 'gray'),
                Tables\Columns\TextColumn::make('product_info')
                    ->label('Product')
                    ->formatStateUsing(function ($record) {
                        if ($record->review) {
                            return $record->review->product->name;
                        } else {
                            return $record->product_name ?? 'Unknown Product';
                        }
                    })
                    ->badge()
                    ->color(fn ($record) => match($record->product_type ?? ($record->review ? $record->review->product->type : 'unknown')) {
                        'game' => 'success',
                        'tech' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('review_author_info')
                    ->label('Review Author')
                    ->formatStateUsing(function ($record) {
                        if ($record->review) {
                            return $record->review->user->name;
                        } else {
                            return $record->review_author_name ?? 'Unknown Author';
                        }
                    })
                    ->searchable(['review_author_name']),
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
                Tables\Filters\TernaryFilter::make('review_deleted')
                    ->label('Review Status')
                    ->placeholder('All Reports')
                    ->trueLabel('Review Deleted')
                    ->falseLabel('Review Active')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNull('review_id'),
                        false: fn (Builder $query) => $query->whereNotNull('review_id'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('approve')
                    ->label('Approve & Delete Review')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Report $record): bool => $record->status === 'pending' && $record->review !== null)
                    ->requiresConfirmation()
                    ->modalHeading('Approve Report')
                    ->modalDescription('This will delete the reported review permanently. The report will be kept for audit purposes. Are you sure?')
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Reports')
                        ->modalHeading('Delete Selected Reports')
                        ->modalDescription('This will permanently delete the selected reports. This action cannot be undone.')
                        ->visible(fn (): bool => Auth::user()->is_admin),
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
