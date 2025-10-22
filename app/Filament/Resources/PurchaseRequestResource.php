<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PurchaseRequest;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PurchaseRequestResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class PurchaseRequestResource extends Resource
{
    protected static ?string $model = PurchaseRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Purchase Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('province')
                                    ->label('Province')
                                    ->required()
                                    ->maxLength(255),
                                Toggle::make('lgu')
                                    ->label('LGU')
                                    ->default(false),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('responsibility_center')
                                    ->label('Responsibility Center')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('account_code')
                                    ->label('Account Code')
                                    ->required()
                                    ->maxLength(50),
                            ]),
                        TextInput::make('department')
                            ->label('Department')
                            ->required()
                            ->maxLength(255),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('pr_no')
                                    ->label('PR Number')
                                    ->maxLength(50),
                                TextInput::make('sai_no')
                                    ->label('SAI Number')
                                    ->maxLength(50),
                                DatePicker::make('date')
                                    ->label('Date')
                                    ->required()
                                    ->default(now()),
                            ]),
                    ]),

                Section::make('Delivery Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('delivery_place')
                                    ->label('Place of Delivery')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('delivery_date_terms')
                                    ->label('Date of Delivery Terms')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Personnel')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('prepared_by')
                                    ->label('Prepared By')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('certified_by')
                                    ->label('Certified By')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('requested_by')
                                    ->label('Requested By')
                                    ->required()
                                    ->maxLength(255),
                                KeyValue::make('approved_by')
                                    ->label('Approved By')
                                    ->keyLabel('Approver')
                                    ->valueLabel('Position')
                                    ->addActionLabel('Add Approver')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('inventory_item_id')
                                            ->label('Inventory Item')
                                            ->relationship('inventoryItem', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                if ($state) {
                                                    $item = \App\Models\InventoryItem::find($state);
                                                    if ($item) {
                                                        $set('item_code', $item->item_code);
                                                        $set('description', $item->name);
                                                        $set('unit', $item->unit->name ?? '');
                                                        $set('unit_cost', $item->unit_cost);
                                                        $set('category', $item->category->name ?? '');
                                                    }
                                                }
                                            })
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Item Name')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('item_code')
                                                    ->label('Item Code')
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(50),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->required()
                                                    ->rows(3),
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
                                                TextInput::make('unit_cost')
                                                    ->label('Unit Cost (₱)')
                                                    ->numeric()
                                                    ->prefix('₱')
                                                    ->required(),
                                            ])
                                            ->createOptionUsing(function (array $data) {
                                                $item = \App\Models\InventoryItem::create($data);
                                                return $item->id;
                                            }),
                                        TextInput::make('item_no')
                                            ->label('Item No.')
                                            ->numeric()
                                            ->default(1)
                                            ->required(),
                                    ]),
                                TextInput::make('item_code')
                                    ->label('Item Code')
                                    ->disabled(fn (callable $get) => !$get('is_custom_item') && $get('inventory_item_id'))
                                    ->dehydrated(),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->rows(2)
                                    ->disabled(fn (callable $get) => !$get('is_custom_item') && $get('inventory_item_id')),
                                Grid::make(4)
                                    ->schema([
                                        Select::make('unit')
                                            ->label('Unit')
                                            ->options(\App\Models\Unit::all()->pluck('name', 'name'))
                                            ->searchable()
                                            ->required(),

                                        TextInput::make('quantity')
                                            ->label('Quantity')
                                            ->numeric()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $unitCost = $get('unit_cost');
                                                if ($state && $unitCost) {
                                                    $set('total_cost', $state * $unitCost);
                                                }
                                            }),
                                        TextInput::make('unit_cost')
                                            ->label('Unit Cost (₱)')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->disabled(fn (callable $get) => !$get('is_custom_item') && $get('inventory_item_id'))
                                            ->dehydrated()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $quantity = $get('quantity');
                                                if ($state && $quantity) {
                                                    $set('total_cost', $state * $quantity);
                                                }
                                            }),
                                        TextInput::make('total_cost')
                                            ->label('Total Cost (₱)')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->disabled()
                                            ->dehydrated(),
                                    ]),
                                Toggle::make('is_custom_item')
                                    ->label('Custom Item')
                                    ->helperText('Enable if this is a custom item not in inventory')
                                    ->live()
                                    ->default(false)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            // Allow editing for custom items
                                        } else {
                                            // Reset to default values when switching back to inventory item
                                            $set('add_to_inventory', false);
                                        }
                                    }),
                                Toggle::make('add_to_inventory')
                                    ->label('Add to Inventory')
                                    ->helperText('Add this custom item to inventory items')
                                    ->visible(fn (callable $get) => $get('is_custom_item'))
                                    ->live()
                                    ->default(false)
                                    ->formatStateUsing(function ($state) {
                                        // Ensure it stays at 0 by default
                                        return $state ? 1 : 0;
                                    }),
                                Textarea::make('description')
                                    ->label('Note')
                                    ->rows(2),
                            ])
                            ->columns(1)
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['description'] ?? null)
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                // Auto-number items
                                $items = $get('items');
                                if (is_array($items)) {
                                    foreach ($items as $index => $item) {
                                        if (!isset($item['item_no']) || $item['item_no'] === null) {
                                            $set("items.{$index}.item_no", $index + 1);
                                        }
                                    }
                                }
                            }),
                    ]),

                Section::make('Summary')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('grand_total')
                                    ->label('Grand Total (₱)')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->disabled()
                                    ->dehydrated(false),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'completed' => 'Completed',
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ]),
                        Textarea::make('description')
                            ->label('Notes')
                            ->rows(3),
                       ]),
                   ]);
           }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('province')
                    ->label('Province')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('PHP')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'info' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'gray' => 'completed',
                    ]),
                TextColumn::make('prepared_by')
                    ->label('Prepared By')
                    ->searchable(),
                TextColumn::make('requested_by')
                    ->label('Requested By')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ]),
                SelectFilter::make('province')
                    ->label('Province')
                    ->options([
                        'Negros Oriental' => 'Negros Oriental',
                        'Cebu' => 'Cebu',
                        'Bohol' => 'Bohol',
                        'Siquijor' => 'Siquijor',
                    ]),
            ])
            ->actions([
               ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(PurchaseRequest $record): bool => $record->status === 'pending')
                    ->action(function (PurchaseRequest $record): void {
                        $record->update(['status' => 'approved']);
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(PurchaseRequest $record): bool => $record->status === 'pending')
                    ->action(function (PurchaseRequest $record): void {
                        $record->update(['status' => 'rejected']);
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
            'index' => Pages\ListPurchaseRequests::route('/'),
            'create' => Pages\CreatePurchaseRequest::route('/create'),
            'view' => Pages\ViewPurchaseRequest::route('/{record}'),
            'edit' => Pages\EditPurchaseRequest::route('/{record}/edit'),
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
