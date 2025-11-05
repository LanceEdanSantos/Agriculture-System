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
                    ->relationship('user', 'fname') 
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
                // Forms\Components\DateTimePicker::make('delivered_at')
                //     ->disabled()
                //     ->visible($isAdminOrManager),
                Forms\Components\Select::make('approved_by')
                    ->relationship('user', 'fname')
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
        return $query;
        // Regular users can only see their own requests (not all requests from their farms)
        // return $query->where('user_id', $user->id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Name')
                    ->formatStateUsing(
                        fn($record) =>
                        trim(implode(' ', [
                            $record->user->fname,
                            $record->user->mname,
                            $record->user->lname,
                            $record->user->suffix
                        ]))
                    )
                    ->sortable(query: function (Builder $query, string $direction) {
                        $query->orderBy('fname', $direction)
                            ->orderBy('mname', $direction)
                            ->orderBy('lname', $direction)
                            ->orderBy('suffix', $direction);
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('user', function ($q) use ($search) {
                            $q->where('fname', 'like', "%{$search}%")
                                ->orWhere('mname', 'like', "%{$search}%")
                                ->orWhere('lname', 'like', "%{$search}%")
                                ->orWhere('suffix', 'like', "%{$search}%");
                        });
                    }),
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
                    ->options(ItemRequest::getStatuses())
                    ->disableOptionWhen(fn(string $value): bool => in_array($value, ['approved', 'rejected'])),
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
                        ->hidden(fn(ItemRequest $record): bool => $record->status === ItemRequest::STATUS_APPROVED)
                        ->color('success')
                        ->label('Approve')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Item Request')
                        ->modalDescription('Confirm stock adjustment before approving this request.')
                        ->form(function (ItemRequest $record) {
                            $item = $record->inventoryItem;
                            $availableStock = $item ? $item->getAvailableStockForOut() : 0;
                            $requestedQty = $record->quantity;
                            $unitName = $item && $item->unit ? $item->unit->name : 'units';

                            return [
                                Forms\Components\Placeholder::make('stock_info')
                                    ->label('Stock Availability')
                                    ->content(function () use ($requestedQty, $availableStock, $unitName) {
                                        $sufficient = $availableStock >= $requestedQty;
                                        $color = $sufficient ? 'success' : 'danger';
                                        $icon = $sufficient ? '✓' : '⚠';

                                        return new \Illuminate\Support\HtmlString(
                                            "<div class='rounded-lg p-4 bg-{$color}-50 dark:bg-{$color}-900/20 border border-{$color}-200 dark:border-{$color}-800'>" .
                                                "<div class='flex items-center gap-2 font-semibold text-{$color}-700 dark:text-{$color}-300'>" .
                                                "<span class='text-xl'>{$icon}</span> " .
                                                "<span>Requested: {$requestedQty} {$unitName}</span>" .
                                                "</div>" .
                                                "<div class='mt-2 text-sm text-{$color}-600 dark:text-{$color}-400'>" .
                                                "Available Stock: <strong>{$availableStock} {$unitName}</strong>" .
                                                ($sufficient ? '' : " <br><span class='text-red-600 font-bold'>Insufficient stock!</span>") .
                                                "</div>" .
                                                "</div>"
                                        );
                                    }),
                                Forms\Components\TextInput::make('approved_quantity')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->default(min($requestedQty, $availableStock))
                                    ->maxValue($availableStock)
                                    ->step(1)
                                    ->suffix($unitName)
                                    ->label('Approved Quantity')
                                    ->helperText("Maximum available: {$availableStock} {$unitName}"),
                                // Forms\Components\Textarea::make('message_to_farmer')
                                //     ->label('Message to Farmer (Optional)')
                                //     ->placeholder('e.g., "Only 30 units are currently available. Do you want to proceed with 30 units or wait for full stock?"')
                                //     ->helperText('This message will be sent to the farmer explaining stock availability')
                                //     ->rows(3)
                                //     ->columnSpanFull(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Internal Approval Notes')
                                    ->placeholder('Internal notes for approval (not visible to farmer)')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ];
                        })
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

                            // Send message to farmer if provided
                            if (!empty($data['message_to_farmer'])) {
                                \App\Models\RequestMessage::create([
                                    'item_request_id' => $record->id,
                                    'user_id' => Auth::id(),
                                    'message' => $data['message_to_farmer'],
                                    'is_admin_message' => true,
                                ]);
                            }

                            // Also send an automatic approval message
                            $autoMessage = $adjusted
                                ? "Your request has been approved for {$approvedQuantity} units (adjusted from {$data['approved_quantity']} due to available stock). The items will be prepared for delivery."
                                : "Your request has been approved for {$approvedQuantity} units. The items will be prepared for delivery.";

                            \App\Models\RequestMessage::create([
                                'item_request_id' => $record->id,
                                'user_id' => Auth::id(),
                                'message' => $autoMessage,
                                'is_admin_message' => true,
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
                        ->hidden(fn(ItemRequest $record): bool => $record->status === ItemRequest::STATUS_APPROVED)
                        ->requiresConfirmation()
                        ->modalHeading('Reject Item Request')
                        ->modalDescription('Provide a reason for rejecting this request.')
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Reason for Rejection')
                                ->placeholder('e.g., "Item is currently out of stock and will not be available for 2 weeks."')
                                ->required()
                                ->rows(3)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('message_to_farmer')
                                ->label('Additional Message to Farmer (Optional)')
                                ->placeholder('e.g., "You may want to consider requesting alternative items that are currently in stock."')
                                ->helperText('This message will be sent to the farmer along with the rejection')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->action(function (ItemRequest $record, array $data) {
                            $record->update([
                                'status' => ItemRequest::STATUS_REJECTED,
                                'rejection_reason' => $data['rejectiKon_reason'],
                            ]);

                            // Log status change
                            $record->statuses()->create([
                                'status' => ItemRequest::STATUS_REJECTED,
                                'changed_by' => Auth::id(),
                                'notes' => 'Request rejected: ' . $data['rejection_reason'],
                            ]);

                            // Send rejection message to farmer
                            $rejectionMessage = "Your request has been rejected. Reason: " . $data['rejection_reason'];

                            \App\Models\RequestMessage::create([
                                'item_request_id' => $record->id,
                                'user_id' => Auth::id(),
                                'message' => $rejectionMessage,
                                'is_admin_message' => true,
                            ]);

                            // Send additional message if provided
                            if (!empty($data['message_to_farmer'])) {
                                \App\Models\RequestMessage::create([
                                    'item_request_id' => $record->id,
                                    'user_id' => Auth::id(),
                                    'message' => $data['message_to_farmer'],
                                    'is_admin_message' => true,
                                ]);
                            }

                            Notification::make()
                                ->title('Request Rejected')
                                ->body('The request has been rejected and the farmer has been notified.')
                                ->success()
                                ->send();
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
            RelationManagers\MessagesRelationManager::class,
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
