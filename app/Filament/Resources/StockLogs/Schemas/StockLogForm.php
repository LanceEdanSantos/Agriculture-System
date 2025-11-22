<?php

namespace App\Filament\Resources\StockLogs\Schemas;

use Closure;
use App\Models\Item;
use App\Enums\TransferType;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Utilities\Get;

class StockLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                                    ->relationship('item', 'name')  
                                    ->getOptionLabelFromRecordUsing(
                                        fn (Model $record) => "{$record->name} ({$record->stock})"
                                    )
                                    ->default(
                                        fn() => request()->query('item') && Item::find(request()->query('item'))
                                            ? request()->query('item')
                                            : null
                                    )
                                    ->reactive()
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
                                            if ($value > $item->stock) {
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
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::id())
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // $state is the selected user_id
                                        $user = \App\Models\User::find($state);
                                        if ($user) {
                                            $set('full_name', $user->name); // autofill the TextInput
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
            ]);
    }
}
