<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Infolists;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class UnitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Unit Name')
                                    ->icon('heroicon-o-tag')
                                    ->weight('bold')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('symbol')
                                    ->label('Symbol')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->badge()
                                    ->color('primary')
                                    ->size('lg'),
                            ]),
                    ])
                    ->collapsible(false),

                Section::make('Description')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->markdown()
                            ->prose()
                            ->hidden(fn($record) => blank($record->description)),
                    ])
                    ->collapsible()
                    ->collapsed(fn($record) => !blank($record->description)),

                Section::make('Usage')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\TextEntry::make('items_count')
                            ->label('Used in Items')
                            ->icon('heroicon-o-cube')
                            ->numeric()
                            ->formatStateUsing(fn($state) => number_format($state) . ' ' . str('item')->plural($state))
                            ->color('gray')
                            ->default(0),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->hidden(fn($record) => !$record->items_count),
            ]);
    }
}
