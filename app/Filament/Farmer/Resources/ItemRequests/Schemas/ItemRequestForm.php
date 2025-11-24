<?php

namespace App\Filament\Farmer\Resources\ItemRequests\Schemas;

use Closure;
use App\Models\Farm;
use App\Models\Item;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use App\Enums\ItemRequestStatus;
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
                            // ->hidden()
                            ->dehydrated(true)
                            ->disabled()
                            ->default(auth()->user()->id)
                            ->relationship('user', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn(Model $record) => "{$record->first_name} {$record->last_name}"
                            )
                            ->required(),
                        Select::make('item_id')
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(function (string $search, callable $get) {
                                $farmId = $get('farm_id'); // get selected farm
                                if (!$farmId) {
                                    return []; // no farm selected → no items
                                }

                                return Item::query()
                                    ->where('name', 'like', "%{$search}%")
                                    // ->where('is_active', true)
                                    ->whereHas('farms', fn ($q) => $q->where('farm_id', $farmId)) // only items linked to farm
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all();
                            })

                            ->options(function (callable $get) {
                                $farmId = $get('farm_id');
                                if (!$farmId) {
                                    return [];
                                }
                                return Item::query()
                                    ->whereHas('farms', fn ($q) => $q->where('farm_id', $farmId))
                                    // ->where('is_active', true)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })

                            ->getOptionLabelFromRecordUsing(
                                fn (Model $record) =>
                                    "{$record->name} | Only {$record->stock} left"
                            )

                            ->required(),
                        Select::make('status')
                            ->options(ItemRequestStatus::class)
                            ->default(ItemRequestStatus::PENDING)
                            ->disabled()
                            ->dehydrated(true)
                            ->required(),    
                    ]),
                Section::make()
                    ->schema([
                        TextInput::make('quantity')
                            ->required()
                            ->live()
                            ->rules([
                                fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                    $item = Item::find($get('item_id'));
                                    dd($item);
                                    if ($item && $item->stock < $value) {
                                        $fail("Only {$item->stock} {$item->name} left. Reduce quantity.");
                                    }
                                },
                            ])
                            ->default(0)
                            ->numeric(),
                       Select::make('farm_id')
                            ->label('Farm')
                            ->searchable()
                            ->preload()
                            // options based on the current user's assigned farms (qualified columns)
                            ->options(function () {
                                $user = auth()->user();
                                if (! $user) {
                                    return [];
                                }

                                // Ensure we select farms table columns explicitly to avoid ambiguity
                                return $user
                                    ->farms()
                                    ->where('farms.is_active', true)
                                    ->select('farms.id', 'farms.name')
                                    ->pluck('farms.name', 'farms.id')
                                    ->toArray();
                            })
                            // relationship assumes the model you're editing has `public function farm() { return $this->belongsTo(Farm::class); }`
                            ->relationship('farm', 'name')
                            // default to the single farm if user has exactly one
                            ->default(function () {
                                $user = auth()->user();
                                if (! $user) {
                                    return null;
                                }
                                $farms = $user->farms()->select('farms.id')->get();
                                return $farms->count() === 1 ? $farms->first()->id : null;
                            })
                            // disable when exactly one farm assigned; but still send the value on submit
                            ->disabled(function () {
                                $user = auth()->user();
                                if (! $user) {
                                    return false;
                                }
                                return $user->farms()->count() === 1;
                            })
                            ->dehydrated() // make sure disabled field is saved
                            ->required(),
                        Textarea::make('notes')
                            // ->required(),
                    ]),
            ]);
    }
}
