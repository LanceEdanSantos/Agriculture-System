<?php

namespace App\Filament\Resources;

use Filament\Forms;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\InventoryItemExporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InventoryItemResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\InventoryItemResource\RelationManagers;
use Filament\Tables\Actions\ActionGroup;

class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Inventory Management';

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
                                ->searchable()
                                ->required(),
                            Select::make('unit_id')
                                ->label('Unit')
                                ->relationship('unit', 'name')
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

                Section::make('Supplier Information')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('supplier_id')
                                ->label('Supplier')
                                ->relationship('supplier', 'name')
                                ->searchable()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label('Supplier Name')
                                        ->required(),
                                    TextInput::make('company_name')
                                        ->label('Company Name'),
                                    Textarea::make('address')
                                        ->label('Address')
                                        ->rows(2),
                                ]),
                            TextInput::make('last_supplier')
                                ->label('Last Supplier')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                        DatePicker::make('last_purchase_date')
                            ->label('Last Purchase Date')
                            ->disabled()
                            ->dehydrated(false),
                    ]),

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
            TextColumn::make('supplier.name')
                ->label('Supplier')
                ->searchable()
                ->sortable()
                ->limit(20)
                ->tooltip(fn($record) => optional(\App\Models\Supplier::find($record->supplier_id))->name ?? 'No supplier'),
                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),
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
                        return "No Stock ({$stock})";
                    } elseif ($stock <= $min) {
                        return "Low Stock ({$stock})";
                    }
                    return "In Stock ({$stock})";
                })
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
                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name'),
                SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name'),
                Filter::make('low_stock')
                    ->label('Low Stock Items')
                    ->query(fn(Builder $query): Builder => $query->whereRaw('current_stock <= minimum_stock')),
                Filter::make('deleted')
                    ->label('Show Deleted Items')
                    ->query(fn(Builder $query): Builder => $query->onlyTrashed()),
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
                Action::make('adjust_stock')
                    ->label('Adjust Stock')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        TextInput::make('quantity')
                            ->label('Quantity to Add/Remove')
                            ->numeric()
                            ->required()
                            ->helperText('Use positive numbers to add stock, negative to remove'),
                    ])
                    ->action(function (InventoryItem $record, array $data): void {
                        $record->update([
                            'current_stock' => $record->current_stock + $data['quantity'],
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
