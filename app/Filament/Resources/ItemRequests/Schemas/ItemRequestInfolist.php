<?php

namespace App\Filament\Resources\ItemRequests\Schemas;

use App\Models\ItemRequest;
use Filament\Schemas\Schema;
use App\Enums\ItemRequestStatus;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Livewire;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;

class ItemRequestInfolist
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
                        Section::make('Request Details')
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Request ID'),
                                TextEntry::make('user.first_name')
                                    ->label('Requested By')
                                    ->formatStateUsing(fn($state, $record) => $record->user->first_name . ' ' . $record->user->middle_name . ' ' . $record->user->last_name . ' ' . $record->user->suffix),
                                TextEntry::make('item.name')
                                    ->label('Item'),
                                TextEntry::make('quantity')
                                    ->numeric(),
                                // TextEntry::make('status')
                                //         ->badge()
                                //         ->color(fn(ItemRequestStatus $state): string => $state->getColor())
                                //         ->formatStateUsing(fn(ItemRequestStatus $state): string => $state->getLabel())
                                //         ->label('Status'),
                                TextEntry::make('farm.name')
                                    ->label('Farm'),
                                TextEntry::make('deleted_at')
                                    ->label('Deleted At')
                                    ->dateTime('M j, Y g:i A')
                                    ->visible(fn(ItemRequest $record): bool => $record->trashed())
                                    ->placeholder('Not deleted'),
                            ]),

                        Section::make('Timestamps')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Requested On')
                                    ->dateTime('M j, Y g:i A')
                                    ->placeholder('Not available'),
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('M j, Y g:i A')
                                    ->placeholder('Not updated yet'),
                            ]),
                        // Section::make('Messages')
                        //     ->schema([
                        //         \Filament\Infolists\Components\HtmlEntry::make('livewire_messages')
                        //             ->content(
                        //                 fn($record) =>
                        //                 \Livewire\Livewire::mount(
                        //                     \App\Livewire\ItemRequest\RequestMessages::class,
                        //                     ['request' => $record]
                        //                 )->html()
                        //             )
                        //             ->columnSpanFull(),
                        //     ])
                        //     ->columnSpanFull(),
                        Section::make('Notes')
                            ->description('Additional information about the request')
                            ->schema([
                                TextEntry::make('notes')
                                    ->markdown()
                                    ->placeholder('No notes provided')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
