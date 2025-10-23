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
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Filament\Resources\ItemRequestResource\Pages;
use App\Filament\Resources\ItemRequestResource\RelationManagers;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

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
                    ->reactive(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->step(1)
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
                    ->relationship('user', 'name')
                    ->label('Approved By')
                    ->searchable()
                    ->disabled()
                    ->preload(),
                Forms\Components\Textarea::make('rejection_reason')
                    ->columnSpanFull()
                    ->disabled(!$isAdminOrManager)
                    ->visible($isAdminOrManager),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        // Super admins and farm managers can see all requests
        if ($user->hasRole('super_admin') || $user->hasRole('farm_manager')) {
            return $query;
        }

        // Regular users can only see their own requests (not all requests from their farms)
        return $query->where('user_id', $user->id);
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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options(
                        collect(ItemRequest::getStatuses())
                            ->except(['approved', 'rejected'])
                            ->toArray()
                    )
                    ->disabled(fn(ItemRequest $record) => in_array(
                        $record->status,
                        [ItemRequest::STATUS_APPROVED, ItemRequest::STATUS_REJECTED]
                    )),
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
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->label('Approve')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Item Request')
                    ->modalDescription('Confirm stock adjustment before approving this request.')
                    ->form([
                        Forms\Components\TextInput::make('approved_quantity')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->step(1)
                            ->label('Approved Quantity'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Approval Notes'),
                    ])
                    ->action(function (ItemRequest $record, array $data) {
                        $item = $record->inventoryItem;

                        if (!$item) {
                            return Notification::make()
                                ->title('No linked inventory item.')
                                ->danger()
                                ->send();
                        }

                        $availableStock = $item->getAvailableStockForOut();

                        $approvedQuantity = min($data['approved_quantity'], $availableStock);
                        $adjusted = $approvedQuantity < $data['approved_quantity'];

                        // Create a stock movement
                        \App\Models\StockMovement::create([
                            'inventory_item_id' => $item->id,
                            'user_id' => Auth::id(),
                            'type' => 'out',
                            'quantity' => $approvedQuantity,
                            'unit_cost' => $item->average_unit_cost ?? 0,
                            'total_cost' => ($item->average_unit_cost ?? 0) * $approvedQuantity,
                            'reason' => 'Item Request Approval',
                            'notes' => $data['notes'] ?? null,
                            'movement_date' => now(),
                        ]);

                        // Update item request
                        $record->update([
                            'status' => ItemRequest::STATUS_APPROVED,
                            'approved_at' => now(),
                            'approved_by' => Auth::id(),
                        ]);

                        // Log status change
                        $record->statuses()->create([
                            'status' => ItemRequest::STATUS_APPROVED,
                            'changed_by' => Auth::id(),
                            'notes' => $data['notes'] ?? 'Approved item request',
                        ]);

                        $msg = $adjusted
                            ? "Approved but adjusted to available stock: {$approvedQuantity} (was {$data['approved_quantity']})"
                            : "Request approved successfully for {$approvedQuantity} units.";

                        Notification::make()
                            ->title('Item Request Approved')
                            ->body($msg)
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                        // ->visible(fn(ItemRequest $record): bool => Auth::user()->can('reject', $record))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Reason for Rejection')
                                ->required(),
                        ])
                        ->action(function (ItemRequest $record, array $data) {
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
