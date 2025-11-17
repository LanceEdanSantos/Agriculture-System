<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

class ItemInfolist
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
                                TextEntry::make('category.name')
                                    ->label('Category'),
                                Grid::make()
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('stock')
                                            ->numeric(),
                                        TextEntry::make('minimum_stock')
                                            ->numeric(),
                                    ]),
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->since()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->since()
                                    ->placeholder('-'),
                            ]),
                        Section::make('Details')
                            ->schema([
                                TextEntry::make('description')
                                    ->markdown(true)
                                    ->placeholder('No description provided')
                                    ->columnSpanFull(),
                                TextEntry::make('notes')
                                    ->markdown(true)
                                    ->placeholder('No notes available')
                                    ->columnSpanFull(),
                                IconEntry::make('active')
                                    ->boolean()
                                    ->label('Is Active'),
                            ])
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
