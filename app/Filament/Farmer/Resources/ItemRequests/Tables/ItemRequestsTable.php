<?php

namespace App\Filament\Farmer\Resources\ItemRequests\Tables;

use Filament\Tables\Table;
use App\Models\ItemRequest;
use App\Enums\ItemRequestStatus;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Enums\Alignment;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;

class ItemRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
               Split::make([
    // Primary info: Item, Status, Quantity
                    Stack::make([
                        TextColumn::make('item.name')
                            ->label('Item')
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable(),
                        TextColumn::make('status')
                            ->badge()
                            ->color(fn(ItemRequestStatus $state): string => $state->getColor())
                            ->formatStateUsing(fn(ItemRequestStatus $state): string => $state->getLabel())
                            ->sortable()
                            ->searchable()
                            ->label('Status'),
                        TextColumn::make('quantity')
                            ->label('Qty')
                            ->numeric()
                            ->sortable()
                            ->alignRight(),
                    ])->grow(),

                    // Secondary info in a collapsible panel: Farm + timestamps
                    Panel::make([
                        Stack::make([
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
                                ->dateTime('M   j, Y g:i A')
                                ->sortable()
                                ->toggleable(isToggledHiddenByDefault: true),
                        ])->space(1)
                    ])->collapsible()->collapsed(true), // Collapsed by default for mobile
                ])->from('sm')
            ])
            ->filters([
                SelectFilter::make('item_id')
                    ->label('Item')
                    ->preload()
                    ->searchable()
                    ->relationship('item', 'name'),
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
                // ActionGroup::make([
                //     ViewAction::make(),
                //     // EditAction::make(),
                //     // DeleteAction::make(),
                //     // RestoreAction::make(),
                //     // ForceDeleteAction::make()
                // ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                    // ForceDeleteBulkAction::make(),
                    // RestoreBulkAction::make(),
                ]),
            ]);
    }
}
