<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Features\FeatureFlag;

class CategoryInfolist
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
                        Section::make('General')
                            ->schema([
                                TextEntry::make('name'),

                                Grid::make()
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->dateTime()
                                            ->since()
                                            ->placeholder('-'),
                                        TextEntry::make('updated_at')
                                            ->dateTime()
                                            ->since()
                                            ->placeholder('-'),
                                    ])
                            ]),
                        Section::make()
                            ->schema([
                                TextEntry::make('description')
                                    ->markdown(true)
                                    ->placeholder('No Description Set')
                                    ->columnSpanFull(),
                                IconEntry::make('active')
                                    ->boolean(),
                            ])
                    ])->columnSpanFull(),


            ]);
    }
}
