<?php

namespace App\Filament\Resources\Farms\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

class FarmInfolist
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
                        Section::make('Farm Details')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Farm Name'),
                                TextEntry::make('slug')
                                    ->label('URL Slug')
                                    ->copyable()
                                    ->copyMessage('Copied to clipboard')
                                    ->copyMessageDuration(1500)
                                    ->placeholder('No slug set'),
                                IconEntry::make('active')
                                    ->label('Active Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle'),
                            ]),

                        Section::make('Timestamps')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime('M j, Y g:i A')
                                    ->placeholder('Not available'),
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('M j, Y g:i A')
                                    ->placeholder('Not updated yet'),
                            ]),

                        Section::make('Description')
                            ->description('Detailed information about the farm')
                            ->schema([
                                TextEntry::make('description')
                                    ->markdown()
                                    ->placeholder('No description provided')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
