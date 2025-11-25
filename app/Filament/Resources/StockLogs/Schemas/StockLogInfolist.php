<?php

namespace App\Filament\Resources\StockLogs\Schemas;

use App\Enums\TransferType;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

class StockLogInfolist
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
                        Section::make('Transaction Details')
                            ->schema([
                                TextEntry::make('item.name')
                                    ->label('Item'),
                                TextEntry::make('type')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        TransferType::IN->value => 'success',
                                        TransferType::OUT->value => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('quantity')
                                    ->formatStateUsing(fn (string $state, $record) => ($record->type === TransferType::IN->value ? '+' : '-') . ' ' . $state)
                                    ->color(fn (string $state, $record) => $record->type === TransferType::IN->value ? 'success' : 'danger'),
                                TextEntry::make('user.first_name')
                                    ->label('Responsible Person')
                                    ->formatStateUsing(fn($state, $record) => $record->user->first_name . ' ' .     $record->user->middle_name . ' ' . $record->user->last_name . ' ' . $record->user->suffix)
                                    ->placeholder('Not specified'),
                            ]),
                        Section::make('Timestamps')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->dateTime('M j, Y g:i A')
                                    ->label('Created')
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime('M j, Y g:i A')
                                    ->label('Last Updated')
                                    ->placeholder('-'),
                            ]),
                        
                        Section::make('Additional Information')
                            ->schema([
                                TextEntry::make('notes')
                                    ->placeholder('No notes available')
                                    ->markdown()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
