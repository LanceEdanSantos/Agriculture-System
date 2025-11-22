<?php

namespace App\Filament\Resources\ItemRequests\Tables;

use Filament\Tables\Table;
use App\Models\ItemRequest;
use Filament\Actions\Action;
use App\Enums\ItemRequestStatus;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use App\Models\User;

class ItemRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Request ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Requested By')
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
                    ->options(ItemRequestStatus::class)
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
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make()
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
