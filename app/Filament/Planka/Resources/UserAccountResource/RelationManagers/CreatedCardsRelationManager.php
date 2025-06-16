<?php

namespace App\Filament\Planka\Resources\UserAccountResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreatedCardsRelationManager extends RelationManager
{
    protected static string $relationship = 'createdCards';
    
    protected static ?string $title = 'Created Cards';
    
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('list_id')
                    ->relationship('list', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('due_date')
                    ->label('Due Date'),
                Forms\Components\Select::make('timer_due_date_type')
                    ->label('Timer Type')
                    ->options([
                        'stopping' => 'Stopping',
                        'tracking' => 'Tracking',
                    ]),
                Forms\Components\Toggle::make('timer_started')
                    ->label('Timer Started'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Card Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),
                Tables\Columns\TextColumn::make('list.board.name')
                    ->label('Board')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('list.name')
                    ->label('List')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('cardMemberships_count')
                    ->label('Members')
                    ->counts('cardMemberships')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->counts('tasks')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('attachments_count')
                    ->label('Attachments')
                    ->counts('attachments')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->where('due_date', '<', now()))
                    ->label('Overdue'),
                Tables\Filters\Filter::make('has_due_date')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('due_date'))
                    ->label('Has Due Date'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}