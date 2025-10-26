<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\InventoryItem;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use App\Filament\Exports\InventoryItemExporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InventoryItemResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\InventoryItemResource\RelationManagers;

class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Inventory Management';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Item Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('item_code')
                                ->label('Item Code')
                                ->unique(ignoreRecord: true)
                                ->maxLength(50),
                        ]),
                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(3),
                        Grid::make(2)->schema([
                            Select::make('category_id')
                                ->label('Category')
                                ->relationship('category', 'name')
                                ->preload()
                                ->searchable()
                                ->required(),
                            Select::make('unit_id')
                                ->label('Unit')
                                ->relationship('unit', 'name')
                                ->preload()
                                ->searchable()
                                ->required(),
                        ]),
                    ]),

                Section::make('Stock Information')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('current_stock')
                                ->label('Current Stock')
                                ->numeric()
                                ->default(0)
                                ->required(),
                            TextInput::make('minimum_stock')
                                ->label('Minimum Stock')
                                ->numeric()
                                ->default(10)
                                ->required(),
                            // TextInput::make('unit_cost')
                            //     ->label('Unit Cost (₱)')
                            //     ->numeric()
                            //     ->prefix('₱')
                            //     ->required(),
                        ]),
                        // Grid::make(2)->schema([
                        //     TextInput::make('average_unit_cost')
                        //         ->label('Average Unit Cost (₱)')
                        //         ->numeric()
                        //         ->prefix('₱')
                        //         ->disabled()
                        //         ->dehydrated(false),
                        //     TextInput::make('total_purchased')
                        //         ->label('Total Purchased')
                        //         ->numeric()
                        //         ->disabled()
                        //         ->dehydrated(false),
                        // ]),
                    ]),

                // Section::make('Supplier Information')
                //     ->schema([
                //         Grid::make(2)->schema([
                //             Select::make('supplier_id')
                //                 ->label('Supplier')
                //                 ->relationship('supplier', 'name')
                //                 ->preload()
                //                 ->searchable()
                //                 ->createOptionForm([
                //                     TextInput::make('name')
                //                         ->label('Supplier Name')
                //                         ->required(),
                //                     TextInput::make('company_name')
                //                         ->label('Company Name'),
                //                     Textarea::make('address')
                //                         ->label('Address')
                //                         ->rows(2),
                //                 ]),
                //             TextInput::make('last_supplier')
                //                 ->label('Last Supplier')
                //                 ->disabled()
                //                 ->dehydrated(false),
                //         ]),
                //         DatePicker::make('last_purchase_date')
                //             ->label('Last Purchase Date')
                //             ->disabled()
                //             ->dehydrated(false),
                //     ]),

                Section::make('Additional Information')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('expiration_date')
                                ->label('Expiration Date'),
                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Item Name')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(30),
                TextColumn::make('current_stock')
                    ->label('Current Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn($state): string => $state <= 0 ? 'No Stock' : ($state <= 10 ? 'Low Stock' : 'In Stock'))
                    ->alignCenter(),
            // TextColumn::make('supplier.name')
            //     ->label('Supplier')
            //     ->searchable()
            //     ->sortable()
            //     ->limit(20)
            //     ->tooltip(fn($record) => optional(\App\Models\Supplier::find($record->supplier_id))->name ?? 'No supplier'),
            
                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),
                    TextColumn::make('notes')
                ->label('Notes')
                ->searchable()
                ->sortable()
                ->alignCenter()
                ->limit(30),
                TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                    BadgeColumn::make('current_stock')
                    ->label('Stock Status')
                    ->sortable()
                    ->formatStateUsing(function (InventoryItem $record) {
                        $stock = $record->current_stock;
                        $min = $record->minimum_stock ?? 10;
                
                        if ($stock <= 0) {
                            $text = "No Stock ({$stock})";
                        } elseif ($stock <= $min) {
                            $text = "Low Stock ({$stock})";
                        } else {
                            $text = "In Stock ({$stock})";
                        }
                
                        // return HTML with bold tag (or span with Tailwind)
                        return "<span class=\"font-bold\">{$text}</span>";
                    })
                    ->html() // <-- allow raw HTML to be rendered
                    ->color(function (InventoryItem $record) {
                        $stock = $record->current_stock;
                        $min = $record->minimum_stock ?? 10;
                
                        return match (true) {
                            $stock <= 0 => 'danger',
                            $stock <= $min => 'warning',
                            default => 'success',
                        };
                    })
                    ->icon(function (InventoryItem $record) {
                        $stock = $record->current_stock;
                        $min = $record->minimum_stock ?? 10;
                
                        return match (true) {
                            $stock <= 0 => 'heroicon-o-x-circle',
                            $stock <= $min => 'heroicon-o-exclamation-triangle',
                            default => 'heroicon-o-check-circle',
                        };
                    })
                    ->alignCenter(),
                // TextColumn::make('expiration_date')
                //     ->label('Expiration')
                //     ->date()
                //     ->sortable()
                //     ->color(fn($state): string => match (true) {
                //         strtotime((string) $state) < time() => 'danger',
                //         strtotime((string) $state) < strtotime('+30 days') => 'warning',
                //         default => 'success',
                //     })
                //     ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', titleAttribute: 'name'),
                // SelectFilter::make('supplier_id')
                //     ->label('Supplier')
                //     ->relationship('supplier', 'name'),
                SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name'),
                Filter::make('low_stock')
                    ->label('Low Stock Items')
                    ->query(fn(Builder $query): Builder => $query->whereRaw('current_stock <= minimum_stock')),
                Filter::make('created_at')
                    ->label('Created At')
                    ->form([
                        DatePicker::make('from')
                            ->label('From'),
                        DatePicker::make('until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                TrashedFilter::make(),
            ])
            ->defaultSort('name')
            ->paginated([10, 25, 50, 100])
            ->headerActions([
                // Action::make('print')
                //     ->label('Print Report')
                //     ->icon('heroicon-o-printer')
                //     ->color('gray')
                //     ->url(fn (): string => route('inventory.print', ['table' => request()->query()]))
                //     ->openUrlInNewTab(),
            ])
            ->actions([
                ActionGroup::make([
                EditAction::make(),
                ForceDeleteAction::make(),
                Action::make('delete_with_reason')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->form([
                        Textarea::make('reason')
                            ->label('Reason for Deletion')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (InventoryItem $record, array $data): void {
                        // Soft delete the item with reason logged
                        $record->delete();

                        // Create a notification for admins
                        $admins = \App\Models\User::role('super_admin')->get();
                        foreach ($admins as $admin) {
                            \Filament\Notifications\Notification::make()
                                ->title('Inventory Item Deleted')
                                ->body("{$record->name} has been deleted. Reason: {$data['reason']}")
                                ->warning()
                                ->sendToDatabase($admin);
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Delete Inventory Item')
                    ->modalDescription('This action can be undone by restoring the item. Please provide a reason for deletion.')
                    ->modalSubmitActionLabel('Delete Item'),
                RestoreAction::make()
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success'),
                // Action::make('create_stock_movement')
                //     ->label('Log Stock')
                //     ->icon('heroicon-o-arrow-path')
                //     ->url(fn(InventoryItem $record) => StockMovementResource::getUrl('create', [
                //         'inventory_item_id' => $record->id, // preselect this item
                //     ]))
                //     ->openUrlInNewTab(),
                Action::make('stock_movement')
    ->label('Add Stock Movement')
    ->icon('heroicon-o-arrow-path')
    ->color('primary')
    ->modalHeading(fn(InventoryItem $record) => 'Record Stock Movement for ' . $record->name)
    ->modalButton('Save Movement')
    ->form([
        \Filament\Forms\Components\Select::make('type')
            ->label('Movement Type')
            ->options([
                'in' => 'Stock In',
                'out' => 'Stock Out',
            ])
            ->required(),
        \Filament\Forms\Components\TextInput::make('quantity')
            ->label('Quantity')
            ->numeric()
            ->required()
            ->minValue(1)
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
                    \Filament\Forms\Components\Textarea::make('notes')
            ->label('Notes')
            ->rows(3),
        \Filament\Forms\Components\DatePicker::make('movement_date')
            ->label('Movement Date')
            ->default(now())
            ->required(),
    ])
    ->action(function (InventoryItem $record, array $data): void {
        // Save stock movement
                $record->stockMovements()->create([
                    'inventory_item_id' => $record->id,
                    'user_id' => auth()->id(),
                    'type' => $data['type'],
                    'quantity' => $data['quantity'],
                    'notes' => $data['notes'] ?? null,
                    'movement_date' => $data['movement_date'],
                ]);
                // Update current stock automatically
                $record->update([
                    'current_stock' => $data['type'] === 'in'
                        ? $record->current_stock + $data['quantity']
                        : $record->current_stock - $data['quantity'],
                ]);
            })
            ->modalWidth('lg')
            ->successNotificationTitle('Stock logged recorded successfully!'),
                // Action::make('adjust_stock')
                //     ->label('Adjust Stock')
                //     ->icon('heroicon-o-plus-circle')
                //     ->form([
                //         TextInput::make('quantity')
                //             ->label('Quantity to Add/Remove')
                //             ->numeric()
                //             ->required()
                //             ->helperText('Use positive numbers to add stock, negative to remove'),
                //     ])
                //     ->action(function (InventoryItem $record, array $data): void {
                //         $record->update([
                //             'current_stock' => $record->current_stock + $data['quantity'],
                //         ]);
                //     }),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(InventoryItemExporter::class)
                        ->label('Export Selected to Excel')
                        ->fileName('inventory-items-' . now()->format('Y-m-d'))
                        ->icon('heroicon-o-arrow-down-tray'),
                ]),
            ]);
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
            'index' => Pages\ListInventoryItems::route('/'),
            'create' => Pages\CreateInventoryItem::route('/create'),
            'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'category', 'item_code', 'unit_id'];
    }

    protected function getTableWrapperAttributes(): array
    {
        return [
            'class' => 'max-h-[600px] overflow-y-auto', // scrollable table area
        ];
    }

    protected function getTableContentAttributes(): array
    {
        return [
            'class' => 'sticky top-0 z-10 bg-white dark:bg-gray-900', // sticky header
        ];
    }
}
