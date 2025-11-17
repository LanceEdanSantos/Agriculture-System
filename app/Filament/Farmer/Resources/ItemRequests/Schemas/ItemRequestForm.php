<?php

namespace App\Filament\Farmer\Resources\ItemRequests\Schemas;

use Closure;
use App\Models\Farm;
use App\Models\Item;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class ItemRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Item Request Information')
                    ->description('Basic details about the item request')
                    ->schema([
                        Select::make('user_id')
                            ->searchable()
                            ->preload()
                            ->hidden()
                            ->disabled()
                            ->default(auth()->user()->id)
                            ->relationship('user', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn(Model $record) => "{$record->name}"
                            )
                            ->required(),
                        Select::make('item_id')
                            ->searchable()
                            ->preload()
                            ->relationship('item', 'name')
                            ->getSearchResultsUsing(fn(string $search): array => Item::query()
                                ->where('name', 'like', "%{$search}%")
                                ->where('is_active', true)
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->getOptionLabelFromRecordUsing(
                                fn(Model $record) => "{$record->name} | Only {$record->stock} left"
                            )
                            ->required(),
                        Toggle::make('status')
                            ->default(true),
                    ]),
                Section::make()
                    ->schema([
                        TextInput::make('quantity')
                            ->required()
                            ->live()
                            ->rules([
                                fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                    $item = Item::find($get('item_id'));
                                    if ($item && $item->stock < $value) {
                                        $fail("Only {$item->stock} {$item->name} left. Reduce quantity.");
                                    }
                                },
                            ])
                            ->default(0)
                            ->numeric(),
                        Select::make('farm_id')
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(fn(string $search): array => Farm::query()
                                ->where('name', 'like', "%{$search}%")
                                ->where('is_active', true)
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->relationship('farm', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn(Model $record) => "{$record->name} "
                            )
                            ->required(),
                        Textarea::make('notes')
                            ->required(),
                    ]),
            ]);
    }
}
