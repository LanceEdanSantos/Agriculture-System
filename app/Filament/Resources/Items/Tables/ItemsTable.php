<?php

namespace App\Filament\Resources\Items\Tables;

use Closure;
use App\Models\Item;
use App\Models\User;
use Filament\Tables\Table;
use App\Enums\TransferType;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Route;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Schemas\Components\Utilities\Get;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('stock')
                    ->numeric()
                    ->badge()
                    ->color(
                        fn($record) => match (true) {
                            $record->stock <= 0 => 'danger',
                            $record->stock < $record->minimum_stock => 'warning',
                            default => 'success'
                        }
                    )
                    ->formatStateUsing(
                        fn($record) => match (true) {
                            $record->stock <= 0 => 'Out of Stock',
                            $record->stock < $record->minimum_stock => 'Low Stock - ' . $record->stock . ' Left',
                            default => $record->stock . ' Left'
                        }
                    )
                    ->sortable(),
                // TextColumn::make('minimum_stock')
                //     ->label('Minimum Stock')
                //     ->badge()
                //     ->color(
                //         fn($record) =>
                //         $record->stock < $record->minimum_stock
                //             ? 'danger'   // red badge
                //             : 'success'  // green badge
                //     )
                //     ->formatStateUsing(
                //         fn($record) =>
                //         "{$record->minimum_stock} Min"
                //     )
                //     ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->markdown(true)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('expiration_date')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                TernaryFilter::make('active'),
                Filter::make('created_at')
                    ->label('Created At')
                    ->schema([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until')
                    ])->query(function (Builder $query, array $data): Builder {
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
                Filter::make('updated_at')
                    ->label('Updated At')
                    ->schema([
                        DatePicker::make('updated_from'),
                        DatePicker::make('updated_until')
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['updated_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['updated_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    })
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('Log Stock')
                        // ->url(fn ($record) => route('filament.admin.resources.stock-logs.create', ['item' => $record->id]))
                        // ->openUrlInNewTab()
                        ->schema([
                            Grid::make()
                                ->columns([
                                    'sm' => 1,
                                    'md' => 2,
                                    'xl' => 2,
                                ])
                                ->schema([
                                    Section::make('Stock Log Details')
                                        ->description('Basic information about the stock movement')
                                        ->schema([
                                            Select::make('item_id')
                                                // ->relationship('item', 'name')
                                                ->default(fn($record) => $record->id)
                                                ->reactive()
                                                ->disabled()
                                                ->dehydrated(true)
                                                ->options(
                                                    fn(callable $get, string $search = null) =>
                                                    \App\Models\Item::query()
                                                        ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
                                                        ->limit(50)
                                                        ->pluck('name', 'id')
                                                        ->toArray()
                                                )
                                                ->searchable()
                                                ->preload()
                                                ->required(),
                                            Select::make('type')
                                                ->options(TransferType::options())
                                                ->required(),
                                            TextInput::make('quantity')
                                                ->required()
                                                ->default(0)
                                                ->rules([
                                                    'min:1',
                                                    'required',
                                                    'integer',
                                                    fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                                        $item = \App\Models\Item::find($get('item_id'));
                                                        if ($value > $item->stock && $get('type') === TransferType::OUT->value) {
                                                            $fail('The :attribute is invalid. The stock is not enough.');
                                                        }
                                                    },
                                                ])
                                        ->numeric()
                                        ->minValue(1),
                                ]),

                            Section::make('Additional Information')
                                ->description('Extra details about this transaction')
                                ->schema([
                                    Select::make('user_id')
                                        // ->relationship('user', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->disabled()
                                        ->dehydrated(true)
                                        ->options(
                                            fn(callable $get, string $search = null) =>
                                            \App\Models\User::query()
                                                ->when($search, fn($q) => $q->where(['first_name', 'last_name'], 'like', "%{$search}%"))
                                                ->limit(50)
                                                ->pluck('first_name', 'id')
                                                ->toArray()
                                        )
                                        ->default(Auth::id())
                                        ->reactive()
                                        ->required()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            // $state is the selected user_id
                                            $user = \App\Models\User::find($state);
                                            if ($user) {
                                                $set('full_name', $user->first_name . ' ' . $user->last_name); // autofill the TextInput
                                            }
                                        }),
                                    TextInput::make('full_name')
                                        ->hidden()
                                        ->disabled()
                                        ->label('Responsible Person'),
                                    Textarea::make('notes')
                                        ->columnSpanFull(),
                                ])
                        ])
                        ->columnSpanFull(),
                        ])
                        ->action(function (Item $record, $data) {
                            $recipients = User::role(['Administrator'])->get();
                            $stockLog = $record->stockLogs()->create($data);
                            Notification::make()
                                ->title('Stock adjusted for item ' . $record->name)
                                ->body('The stock of item ' . $record->name . ' has been adjusted. Click below to view it.')
                                ->actions([
                                    Action::make('Mark as read')
                                        ->button()
                                        ->markAsRead(true),
                                    Action::make('view')
                                        ->label('View Record')
                                        ->url(route('filament.admin.resources.stock-logs.view', ['record' => $stockLog->id]))
                                        ->openUrlInNewTab(false), // open inside Filament panel
                                ])
                                ->sendToDatabase($recipients);
                            Notification::make()
                                ->title('Stock adjusted for item ' . $record->name)
                                ->success()
                                ->send();
                        })
                        ->label('Add Stock Log')
                        ->hidden(fn($record) => $record->deleted_at !== null)
                        ->icon('heroicon-o-plus-circle'),

                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                    ->exporter(\App\Filament\Exports\ItemExporter::class)
                    ->label('Export')
                    ->icon('heroicon-o-document-text'),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
