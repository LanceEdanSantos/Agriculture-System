<?php

namespace App\Filament\Resources\ItemRequestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Colors\Color;
use Filament\Infolists\Components\TextEntry;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';
    protected static ?string $title = 'Request Discussion';
    protected static ?string $recordTitleAttribute = 'message';

    // Enable polling for real-time updates (every 5 seconds)
    protected static ?string $pollingInterval = '5s';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('message')
                    ->label('Your Message')
                    ->placeholder('Type your message here...')
                    ->required()
                    ->rows(3)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('From')
                    ->icon(fn($record) => $record->is_admin_message ? 'heroicon-o-shield-check' : 'heroicon-o-user')
                    ->iconColor(fn($record) => $record->is_admin_message ? 'success' : 'primary')
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->wrap()
                    ->limit(100)
                    ->searchable(),
                Tables\Columns\IconColumn::make('read_at')
                    ->label('Read')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->getStateUsing(fn($record) => !is_null($record->read_at)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->since()
                    ->tooltip(fn($record) => $record->created_at->format('M j, Y g:i A')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_admin_message')
                    ->label('Message Type')
                    ->options([
                        1 => 'Admin Messages',
                        0 => 'Farmer Messages',
                    ]),
                Tables\Filters\Filter::make('unread')
                    ->query(fn(Builder $query): Builder => $query->whereNull('read_at'))
                    ->label('Unread Only'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Send Message')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    })
                    ->successNotificationTitle('Message sent successfully')
                    ->modalWidth('md')
                    ->modalHeading('Send a Message')
                    ->modalSubmitActionLabel('Send'),
            ])
            ->actions([
                Tables\Actions\Action::make('markAsRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => is_null($record->read_at) && $record->user_id !== Auth::id())
                    ->action(fn($record) => $record->markAsRead()),
                Tables\Actions\ViewAction::make()
                    ->modalHeading('View Message')
                    ->modalContent(fn($record) => view('filament.resources.item-request.view-message', ['record' => $record]))
                    ->modalWidth('md'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->user_id === Auth::id() || Auth::user()->hasRole(['super_admin', 'farm_manager'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->hasRole(['super_admin', 'farm_manager'])),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('5s')
            ->emptyStateHeading('No messages yet')
            ->emptyStateDescription('Start a conversation by sending a message.')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
