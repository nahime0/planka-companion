<?php

namespace App\Filament\Planka\Resources\CardResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NotificationLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'notificationLogs';
    
    protected static ?string $title = 'Notification History';
    
    protected static ?string $icon = 'heroicon-o-bell';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('notification_text')
                    ->label('Notification Text')
                    ->required()
                    ->columnSpanFull()
                    ->rows(4),
                Forms\Components\Textarea::make('custom_message')
                    ->label('Custom Message')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('created_at')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Notified User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notification_text')
                    ->label('Notification')
                    ->wrap()
                    ->limit(100)
                    ->tooltip(fn ($state) => $state),
                Tables\Columns\TextColumn::make('custom_message')
                    ->label('Custom Message')
                    ->placeholder('No custom message')
                    ->wrap()
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('channel')
                    ->label('Channel')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'telegram' => 'info',
                        'email' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('channel')
                    ->options([
                        'telegram' => 'Telegram',
                        'email' => 'Email',
                    ]),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}