<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UnitResource\Pages;
use App\Models\Category;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Inventory Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Unit Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Unit Name')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            TextInput::make('abbreviation')
                                ->label('Abbreviation')
                                ->maxLength(50),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('category_id')
                                ->label('Category')
                                ->relationship('category', 'name')
                                ->preload()
                                ->searchable()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique('categories', 'name'),
                                    TextInput::make('description')
                                        ->maxLength(65535),
                                ]),
                            Toggle::make('is_custom')
                                ->label('Custom Unit')
                                ->default(false),
                        ]),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Unit Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('abbreviation')
                    ->label('Abbreviation')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('primary'),
                BadgeColumn::make('is_custom')
                    ->label('Type')
                    ->colors([
                        'success' => true,
                        'warning' => false,
                    ])
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Custom' : 'Standard'),
                BadgeColumn::make('is_active')
                    ->label('Status')
                    ->colors([
                        'success' => true,
                        'warning' => false,
                    ])
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive'),
                TextColumn::make('inventory_items_count')
                    ->label('Items Using')
                    ->counts('inventoryItems')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id')),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),
                // Filter::make('custom_units')
                //     ->label('Custom Units Only')
                //     ->query(fn(Builder $query): Builder => $query->where('is_custom', true)),
                // Filter::make('standard_units')
                //     ->label('Standard Units Only')
                //     ->query(fn(Builder $query): Builder => $query->where('is_custom', false)),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn(Unit $record): bool => $record->inventoryItems()->count() === 0),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
