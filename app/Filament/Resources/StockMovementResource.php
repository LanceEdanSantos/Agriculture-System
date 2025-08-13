<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Inventory Management';

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
                                    ->required(),
                                Select::make('type')
                                    ->label('Movement Type')
                                    ->options([
                                        'in' => 'Stock In',
                                        'out' => 'Stock Out',
                                    ])
                                    ->required(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required(),
                                TextInput::make('unit_cost')
                                    ->label('Unit Cost (₱)')
                                    ->numeric()
                                    ->prefix('₱'),
                                TextInput::make('total_cost')
                                    ->label('Total Cost (₱)')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                        Textarea::make('reason')
                            ->label('Reason')
                            ->required()
                            ->rows(2),
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
                    ->formatStateUsing(fn (string $state): string => $state === 'in' ? 'Stock In' : 'Stock Out'),
                TextColumn::make('formatted_quantity')
                    ->label('Quantity')
                    ->alignCenter(),
                TextColumn::make('formatted_unit_cost')
                    ->label('Unit Cost')
                    ->alignCenter(),
                TextColumn::make('formatted_total_cost')
                    ->label('Total Cost')
                    ->alignCenter(),
                TextColumn::make('reason')
                    ->label('Reason')
                    ->searchable(),
                TextColumn::make('movement_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
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
                        DatePicker::make('moved_from')
                            ->label('Moved From'),
                        DatePicker::make('moved_until')
                            ->label('Moved Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['moved_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('movement_date', '>=', $date),
                            )
                            ->when(
                                $data['moved_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('movement_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'view' => Pages\ViewStockMovement::route('/{record}'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}