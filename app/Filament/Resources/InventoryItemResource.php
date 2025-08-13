<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\InventoryItem;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InventoryItemResource\Pages;
use App\Filament\Resources\InventoryItemResource\RelationManagers;

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
                            TextInput::make('unit_cost')
                                ->label('Unit Cost (₱)')
                                ->numeric()
                                ->prefix('₱')
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('average_unit_cost')
                                ->label('Average Unit Cost (₱)')
                                ->numeric()
                                ->prefix('₱')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('total_purchased')
                                ->label('Total Purchased')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(false),
                        ]),
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
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'discontinued' => 'Discontinued',
                                ])
                                ->default('active')
                                ->required(),
                        ]),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3),
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
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('current_stock')
                    ->label('Current Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('total_stock_value')
                    ->label('Total Value')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'discontinued' => 'Discontinued',
                    ])->selectablePlaceholder(false),
                TextColumn::make('expiration_date')
                    ->label('Expiration')
                    ->date()
                    ->sortable()
                    ->color(fn(string $state): string => match (true) {
                        strtotime($state) < time() => 'danger',
                        strtotime($state) < strtotime('+30 days') => 'warning',
                        default => 'success',
                    }),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', titleAttribute: 'name'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'discontinued' => 'Discontinued',
                    ]),
                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name'),
                SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name'),
                Filter::make('low_stock')
                    ->label('Low Stock Items')
                    ->query(fn(Builder $query): Builder => $query->whereRaw('current_stock <= minimum_stock')),
                Filter::make('expired')
                    ->label('Expired Items')
                    ->query(fn(Builder $query): Builder => $query->where('expiration_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('adjust_stock')
                    ->label('Adjust Stock')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        TextInput::make('quantity')
                            ->label('Quantity to Add/Remove')
                            ->numeric()
                            ->required()
                            ->helperText('Use positive numbers to add stock, negative to remove'),
                        Textarea::make('reason')
                            ->label('Reason for Adjustment')
                            ->required(),
                    ])
                    ->action(function (InventoryItem $record, array $data): void {
                        $record->update([
                            'current_stock' => $record->current_stock + $data['quantity'],
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
}
