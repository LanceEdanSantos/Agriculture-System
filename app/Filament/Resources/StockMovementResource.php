<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\StockMovementResource\Pages\ListStockMovements;
use App\Filament\Resources\StockMovementResource\Pages\CreateStockMovement;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Inventory Management';

    protected static ?string $navigationLabel = 'History Log';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Movement Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('inventory_item_id')
                                    ->label('Inventory Item')
                                    ->relationship('inventoryItem', 'name')
                                    ->searchable()
                                    ->default(fn() => request()->query('inventory_item_id'))
                                    ->required(),
                                Select::make('type')
                                    ->label('Action')
                                    ->options([
                                        'in' => 'Stock In',
                                        'out' => 'Stock Out',
                                    ])
                                    ->required(),
                            ]),
                        Grid::make(1)
                            ->schema([
                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->live(debounce: 500)
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $inventoryItemId = $get('inventory_item_id');
                                            $type = $get('type');

                                            if (! $inventoryItemId || ! $type) {
                                                return;
                                            }

                                            $inventoryItem = InventoryItem::find($inventoryItemId);

                                            if (! $inventoryItem) {
                                                return;
                                            }

                                            if ($type === 'out') {
                                                $available = $inventoryItem->getAvailableStockForOut();

                                                if ($available <= 0) {
                                                    $fail("{$inventoryItem->name} has no stock available.");
                                                    return;
                                                }

                                                if ($value > $available) {
                                                    $fail("Insufficient stock for {$inventoryItem->name}. Available: {$available} {$inventoryItem->unit?->name}");
                                                }
                                            } elseif ($type === 'in' && $value <= 0) {
                                                $fail('Quantity must be greater than zero for stock-in.');
                                            }
                                        },
                                    ]),
                            ]),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3),
                        DatePicker::make('movement_date')
                            ->label('Movement Date')
                            ->required()
                            ->default(now()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inventoryItem.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'in',
                        'danger' => 'out',
                    ])
                    ->formatStateUsing(fn(string $state): string => $state === 'in' ? 'Stock In' : 'Stock Out'),
                TextColumn::make('formatted_quantity')
                    ->label('Quantity')
                    ->alignCenter(),
                TextColumn::make('movement_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Movement Type')
                    ->options([
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                    ]),
                SelectFilter::make('inventory_item_id')
                    ->label('Inventory Item')
                    ->relationship('inventoryItem', 'name'),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('moved_from')->label('Moved From'),
                        DatePicker::make('moved_until')->label('Moved Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['moved_from'], fn(Builder $query, $date) => $query->whereDate('movement_date', '>=', $date))
                            ->when($data['moved_until'], fn(Builder $query, $date) => $query->whereDate('movement_date', '<=', $date));
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->modalHeading('View Stock Movement')
                        ->modalWidth('lg'),
                    Tables\Actions\EditAction::make()
                        ->modalHeading('Edit Stock Movement')
                        ->modalWidth('lg'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
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
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Tables\Actions\CreateAction::make()
    //             ->modalHeading('Create Stock Movement')
    //             ->modalWidth('lg')
    //             ->icon('heroicon-o-plus')
    //             ->mutateFormDataUsing(function (array $data) {
    //                 // Set the logged-in user ID using the same logic as your CreateStockMovement page
    //                 $data['user_id'] = auth()->id();

    //                 // Optional: keep your unit_cost / total_cost logic if needed
    //                 if (isset($data['unit_cost']) && isset($data['quantity'])) {
    //                     $data['total_cost'] = $data['unit_cost'] * $data['quantity'];
    //                 }

    //                 return $data;
    //             }),
    //     ];
    // }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'create' => CreateStockMovement::route('/create'),
            // The create/edit/view pages are no longer needed since modals handle them
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
