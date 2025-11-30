<?php

namespace App\Filament\Resources\ItemRequests\Tables;

use App\Models\User;
use App\Models\StockLog;
use Filament\Tables\Table;
use App\Enums\TransferType;
use App\Models\ItemRequest;
use Illuminate\Support\Str;
use App\Actions\SendMessage;
use Filament\Actions\Action;
use App\Enums\ItemRequestStatus;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use App\Models\ItemRequestMessage;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use App\Filament\Exports\ItemRequestExporter;

class ItemRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('id')
                //     ->label('Request ID')
                //     ->searchable()
                //     ->sortable(),
                TextColumn::make('user.first_name')
                    ->label('Requested By')
                    ->state(function (ItemRequest $record): string {
                        $name = Str::headline($record['user']['first_name'] . ' ' . $record['user']['middle_name'] . ' ' . $record['user']['last_name'] . ' ' . $record['user']['suffix']) . ' (' . $record['user']['number'] . ')';

                        return "{$name}";
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->alignRight(),
                // TextColumn::make('status')
                //     ->badge()
                //     ->color(fn(ItemRequestStatus $state) => $state->getColor())
                //     ->formatStateUsing(fn(ItemRequestStatus $state) => $state->getLabel())
                //     ->sortable()
                //     ->searchable()
                //     ->label('Status'),
                SelectColumn::make('status')
                    ->options(
                        collect(\App\Enums\ItemRequestStatus::cases())
                            ->mapWithKeys(fn($case) => [
                                $case->value => $case->getLabel(),
                            ])
                            ->toArray()
                    )
                    ->afterStateUpdated(function ($record, $state) {
                        $recipients = User::role(['Administrator'])->get();
                        if ($state == ItemRequestStatus::APPROVED->value) {
                            StockLog::create([
                                'user_id' => Auth::user()->id,
                                'item_id' => $record->item_id,
                                'quantity' => $record->quantity,
                                'type' => TransferType::OUT->value,
                                'item_request_id' => $record->id,
                            ]);
                            if ($record->user->number) {
                                Log::info('Sending SMS to ' . $record->user->number);
                                (new SendMessage())->execute($record->user->number, 'The item you requested has been approved. See details below.
                                                    
                                                    Item: ' . $record->item->name . '
                                                    Quantity: ' . $record->quantity . '
                                                    Farm: ' . $record->farm->name . '
                                                    Requested On: ' . $record->created_at->format('M j, Y g:i A') . '
                                                    
                                                    This is an automated message. Please do not reply to this message.');
                            }
                            // (new SendMessage())->execute($recipients, 'An item request has been updated. Click below to view it.');
                        }
                        if ($state == ItemRequestStatus::REJECTED->value) {
                            // StockLog::create([
                            //     'user_id' => $record->user_id,
                            //     'item_id' => $record->item_id,
                            //     'quantity' => $record->quantity,
                            //     'type' => TransferType::IN->value,
                            //     'item_request_id' => $record->id,
                            // ]);
                            if ($record->user->number) {
                                Log::info('Sending SMS to ' . $record->user->number);
                                (new SendMessage())->execute($record->user->number, 'The item you requested has been rejected. See details below.
                                                    
                                                    Item: ' . $record->item->name . '
                                                    Quantity: ' . $record->quantity . '
                                                    Farm: ' . $record->farm->name . '
                                                    Requested On: ' . $record->created_at->format('M j, Y g:i A') . '
                                                    
                                                    This is an automated message. Please do not reply to this message.');
                            }
                        }
                        Notification::make()
                            ->title('Item Request Updated')
                            ->body('An item request has been updated. Click below to view it.')
                            ->actions([
                                Action::make('Mark as read')
                                    ->button()
                                    ->markAsRead(true),
                                Action::make('view')
                                    ->label('View Request')
                                    ->url(route('filament.admin.resources.item-requests.view', ['record' => $record->id]))
                                    ->openUrlInNewTab(false), // open inside Filament panel
                            ])
                            ->sendToDatabase($recipients);
                        Notification::make()
                            ->title('Status Updated')
                            ->success()
                            ->body('The status of the item request ' . $record->id . ' has been updated to ' . $state)
                            ->actions([
                                Action::make('view')
                                    ->button()
                                    ->url(route('filament.admin.resources.item-requests.view', ['record' => $record->id]))
                                    ->openUrlInNewTab(false)
                                    ->markAsRead(),
                            ])
                            ->send();
                    })
                    ->rules(['required'])
                    ->disabled(function ($record) {
                        return $record->trashed() || in_array($record->status, [
                            ItemRequestStatus::APPROVED,
                            ItemRequestStatus::REJECTED,
                        ]);
                    })
                    ->native(false),
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Requested On')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(ItemRequestStatus::cases())
                        ->reject(fn($case) => $case === ItemRequestStatus::FULFILLED)
                        ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
                        ->toArray())
                    ->multiple(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                TrashedFilter::make()
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('approveRequest')
                        ->label('Approve Request')
                        ->color('success')
                        ->form([
                            TextInput::make('quantity')
                                ->label('Quantity')
                                ->numeric()
                                ->required()
                                ->default(fn($record) => $record->quantity),
                            Textarea::make('message')
                                ->label('Message / Reason')
                                ->required()
                                ->default(fn($record) => "Your request for {$record->item->name} has been approved."),
                        ])
                        ->modalHeading('Approve Item Request')
                        ->modalButton('Approve')
                        ->action(function ($record, array $data) {

                            $previousStock = $record->item->stock;
                            $quantity = $data['quantity'];
                            $customMessage = $data['message'];

                            // Update status and quantity
                            $record->status = ItemRequestStatus::APPROVED->value;
                            $record->quantity = $quantity;
                            $record->save();

                            // Adjust stock
                            // $record->item->decrement('stock', $quantity);

                            // Log stock change
                            StockLog::create([
                                'user_id' => Auth::user()->id,
                                'item_id' => $record->item_id,
                                'quantity' => $quantity,
                                'type' => TransferType::OUT->value,
                                'item_request_id' => $record->id,
                            ]);

                            // Save the message in your model
                            ItemRequestMessage::create([
                                'item_request_id' => $record->id,
                                'user_id' => Auth::user()->id,
                                'message' => $customMessage . "\nStock requested: {$previousStock}\nStock given: {$record->item->stock}",
                            ]);

                            // Send SMS if the user has a number
                            if ($record->user->number) {
                                Log::info('Sending SMS to ' . $record->user->number);
                                (new \App\Actions\SendMessage())->execute(
                                    $record->user->number,
                                    "Your stock has been approved.\n\n$customMessage\n\nStock requested: {$previousStock}\nStock given: {$record->item->stock}"
                                );
                            }
                        })
                        ->visible(function ($record) {
                            return $record->status === ItemRequestStatus::PENDING->value &&
                                $record->item->stock >= $record->quantity;
                        })
                        // ->tooltip(function ($record) {
                        //     if ($record->status !== ItemRequestStatus::PENDING->value) {
                        //         return 'This request is no longer pending';
                        //     }
                        //     if ($record->item->stock < $record->quantity) {
                        //         return 'Insufficient stock available';
                        //     }
                        //     return null;
                        // })
                        ,
                    Action::make('rejectRequest')
                        ->label('Reject Request')
                        ->color('danger')
                        ->form([
                            Textarea::make('reason')
                                ->label('Reason for Rejection')
                                ->required()
                                ->default(fn($record) => "Your request for {$record->item->name} has been rejected."),
                        ])
                        ->modalHeading('Reject Item Request')
                        ->modalButton('Reject')
                        ->action(function ($record, array $data) {
                            $record->status = ItemRequestStatus::REJECTED->value;
                            $record->save();

                            // Save rejection reason
                            ItemRequestMessage::create([
                                'item_request_id' => $record->id,
                                'user_id' => Auth::user()->id,
                                'message' => "Request rejected. Reason: " . $data['reason'],
                            ]);

                            // Send notification
                            if ($record->user->number) {
                                (new SendMessage())->execute(
                                    $record->user->number,
                                    "Your request for {$record->item->name} has been rejected.\n\nReason: {$data['reason']}"
                                );
                            }

                            Notification::make()
                                ->title('Request Rejected')
                                ->success()
                                ->send();
                        })
                        ->visible(fn($record) => $record->status !== ItemRequestStatus::REJECTED->value),
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make()
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Export')
                        ->exporter(ItemRequestExporter::class),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
