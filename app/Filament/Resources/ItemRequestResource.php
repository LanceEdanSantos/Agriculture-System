<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ItemRequest;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\ItemRequestResource\Pages;
use App\Filament\Resources\ItemRequestResource\RelationManagers;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\ActionGroup;

class ItemRequestResource extends Resource
{
    protected static ?string $model = ItemRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $isCreating = $form->getOperation() === 'create';
        $isEditing = $form->getOperation() === 'edit';
        $record = $form->getRecord();

        $canEdit = $isCreating || ($record && $user->can('update', $record));
        $isAdminOrManager = $user->hasRole('super_admin') || $user->hasRole('farm_manager');
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->default(fn() => Auth::id())
                    ->disabled(!$isAdminOrManager)
                    ->preload()
                    ->visible($isAdminOrManager),
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->disabled(!$canEdit),
                Forms\Components\Select::make('inventory_item_id')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->disabled(!$canEdit)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // You can add logic here to update other fields when inventory item changes
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->disabled(!$canEdit),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull()
                    ->disabled(!$canEdit),
                Forms\Components\Select::make('status')
                    ->options(ItemRequest::getStatuses())
                    ->required()
                    ->default(ItemRequest::STATUS_PENDING)
                    ->disabled(!$isAdminOrManager)
                    ->visible($isAdminOrManager),
                Forms\Components\DateTimePicker::make('requested_at')
                    ->default(now())
                    ->disabled(!$isAdminOrManager)
                    ->visible($isAdminOrManager),
                Forms\Components\DateTimePicker::make('approved_at')
                    ->disabled()
                    ->visible($isAdminOrManager),
                Forms\Components\DateTimePicker::make('delivered_at')
                    ->disabled()
                    ->visible($isAdminOrManager),
                Forms\Components\Select::make('approved_by')
                    ->relationship('approver', 'name')
                    ->searchable()
                    ->disabled()
                    ->preload()
                    ->visible($isAdminOrManager),
                Forms\Components\Textarea::make('rejection_reason')
                    ->columnSpanFull()
                    ->disabled(!$isAdminOrManager)
                    ->visible($isAdminOrManager),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!Auth::user()->hasRole('super_admin') && !Auth::user()->hasRole('farm_manager')) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('farm.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('inventoryItem.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        ItemRequest::STATUS_PENDING => 'gray',
                        ItemRequest::STATUS_APPROVED => 'info',
                        ItemRequest::STATUS_IN_DELIVERY => 'warning',
                        ItemRequest::STATUS_DELIVERED => 'success',
                        ItemRequest::STATUS_REJECTED => 'danger',
                        ItemRequest::STATUS_CANCELLED => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => ItemRequest::getStatuses()[$state] ?? $state),
                Tables\Columns\TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('delivered_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('attachments_count')
                    ->label('Attachments')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn(ItemRequest $record): string => $record->attachments()->count() . ' files')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ItemRequest::getStatuses()),
                Tables\Filters\SelectFilter::make('farm')
                    ->relationship('farm', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('requested_at')
                    ->form([
                        Forms\Components\DatePicker::make('requested_from'),
                        Forms\Components\DatePicker::make('requested_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['requested_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('requested_at', '>=', $date),
                            )
                            ->when(
                                $data['requested_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('requested_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->visible(fn(ItemRequest $record): bool => Auth::user()->can('view', $record)),
                    Tables\Actions\EditAction::make()
                        ->visible(fn(ItemRequest $record): bool => Auth::user()->can('update', $record)),
                    Tables\Actions\Action::make('approve')
                        ->visible(fn(ItemRequest $record): bool => Auth::user()->can('approve', $record))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (ItemRequest $record) {
                            if (!Auth::user()->can('approve', $record)) {
                                throw new \Exception('You are not authorized to approve this request.');
                            }
                            $record->update([
                                'status' => ItemRequest::STATUS_APPROVED,
                                'approved_at' => now(),
                                'approved_by' => Auth::id(),
                            ]);

                            // Log status change
                            $record->statuses()->create([
                                'status' => ItemRequest::STATUS_APPROVED,
                                'changed_by' => Auth::id(),
                                'notes' => 'Request approved',
                            ]);
                        }),
                    Tables\Actions\Action::make('reject')
                        ->visible(fn(ItemRequest $record): bool => Auth::user()->can('reject', $record))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Reason for Rejection')
                                ->required(),
                        ])
                        ->action(function (ItemRequest $record, array $data) {
                            if (!Auth::user()->can('reject', $record)) {
                                throw new \Exception('You are not authorized to reject this request.');
                            }
                            $record->update([
                                'status' => ItemRequest::STATUS_REJECTED,
                                'rejection_reason' => $data['rejection_reason'],
                            ]);

                            // Log status change
                            $record->statuses()->create([
                                'status' => ItemRequest::STATUS_REJECTED,
                                'changed_by' => Auth::id(),
                                'notes' => 'Request rejected: ' . $data['rejection_reason'],
                            ]);
                        }),
                    ActivityLogTimelineTableAction::make('Activities')
                        ->timelineIcons([
                            'created' => 'heroicon-m-check-badge',
                            'updated' => 'heroicon-m-pencil-square',
                            'deleted' => 'heroicon-m-trash',
                        ])
                        ->timelineIconColors([
                            'created' => 'success',
                            'updated' => 'warning',
                            'deleted' => 'danger',
                        ])
                        ->limit(20),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if (Auth::user()->can('delete', $record)) {
                                    $record->delete();
                                }
                            });
                        }),
                ]),
            ])
            ->defaultSort('requested_at', 'desc');
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
            'index' => Pages\ListItemRequests::route('/'),
            'create' => Pages\CreateItemRequest::route('/create'),
            'view' => Pages\ViewItemRequest::route('/{record}'),
            'edit' => Pages\EditItemRequest::route('/{record}/edit'),
        ];
    }
}
