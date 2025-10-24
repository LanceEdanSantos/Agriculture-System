<?php

namespace App\Filament\Resources\ItemRequestResource\Pages;

use App\Filament\Resources\ItemRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Colors\Color;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;

class ViewItemRequest extends ViewRecord
{
    protected static string $resource = ItemRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Request Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Requested By')
                                    ->icon('heroicon-o-user')
                                    ->weight('bold'),
                                TextEntry::make('farm.name')
                                    ->label('Farm')
                                    ->icon('heroicon-o-home')
                                    ->weight('bold'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        ItemRequest::STATUS_PENDING => 'warning',
                                        ItemRequest::STATUS_APPROVED => 'success',
                                        ItemRequest::STATUS_IN_DELIVERY => 'info',
                                        ItemRequest::STATUS_DELIVERED => 'success',
                                        ItemRequest::STATUS_REJECTED => 'danger',
                                        ItemRequest::STATUS_CANCELLED => 'gray',
                                        default => 'gray',
                                    }),
                            ]),
                    ])
                    ->collapsible(),
                Section::make('Item Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('inventoryItem.name')
                                    ->label('Item Name')
                                    ->icon('heroicon-o-cube')
                                    ->weight('bold'),
                                TextEntry::make('quantity')
                                    ->label('Requested Quantity')
                                    ->icon('heroicon-o-calculator')
                                    ->suffix(fn($record) => ' ' . ($record->inventoryItem->unit->name ?? 'units'))
                                    ->color('primary')
                                    ->weight('bold'),
                                TextEntry::make('inventoryItem.current_stock')
                                    ->label('Available Stock')
                                    ->icon('heroicon-o-archive-box')
                                    ->suffix(fn($record) => ' ' . ($record->inventoryItem->unit->name ?? 'units'))
                                    ->color(fn($record) => $record->inventoryItem->current_stock >= $record->quantity ? 'success' : 'danger')
                                    ->weight('bold')
                                    ->badge()
                                    ->helperText(fn($record) => $record->inventoryItem->current_stock < $record->quantity
                                        ? 'Insufficient stock! Only ' . $record->inventoryItem->current_stock . ' available.'
                                        : 'Sufficient stock available.'),
                            ]),
                        TextEntry::make('notes')
                            ->label('Request Notes')
                            ->columnSpanFull()
                            ->placeholder('No notes provided')
                            ->icon('heroicon-o-document-text'),
                    ])
                    ->collapsible(),
                Section::make('Timeline')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('requested_at')
                                    ->label('Requested At')
                                    ->dateTime('M j, Y g:i A')
                                    ->icon('heroicon-o-clock'),
                                TextEntry::make('approved_at')
                                    ->label('Approved At')
                                    ->dateTime('M j, Y g:i A')
                                    ->icon('heroicon-o-check-circle')
                                    ->placeholder('Not yet approved'),
                                TextEntry::make('delivered_at')
                                    ->label('Delivered At')
                                    ->dateTime('M j, Y g:i A')
                                    ->icon('heroicon-o-truck')
                                    ->placeholder('Not yet delivered'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('approvedBy.name')
                                    ->label('Approved By')
                                    ->icon('heroicon-o-user-circle')
                                    ->placeholder('Not yet approved'),
                                TextEntry::make('rejection_reason')
                                    ->label('Rejection Reason')
                                    ->icon('heroicon-o-x-circle')
                                    ->color('danger')
                                    ->placeholder('N/A')
                                    ->visible(fn($record) => $record->status === ItemRequest::STATUS_REJECTED),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn($record) => $record->status === ItemRequest::STATUS_PENDING && Auth::user()->can('update', $record)),
        ];
    }
}
