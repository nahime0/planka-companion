<?php

namespace App\Filament\Planka\Resources\ListModelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CardsRelationManager extends RelationManager
{
    protected static string $relationship = 'cards';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->rows(3),
                Forms\Components\DateTimePicker::make('due_date'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),
                Tables\Columns\TextColumn::make('position')
                    ->sortable()
                    ->numeric(decimalPlaces: 0),
                Tables\Columns\TextColumn::make('labels.name')
                    ->badge()
                    ->separator(', '),
                Tables\Columns\TextColumn::make('cardMemberships_count')
                    ->counts('cardMemberships')
                    ->label('Members')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('comments_count')
                    ->counts('comments')
                    ->label('Comments')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('attachments_count')
                    ->counts('attachments')
                    ->label('Files')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($state) => $state && $state < now() ? 'danger' : null),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_due_date')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('due_date')),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->where('due_date', '<', now())),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => route('filament.planka.resources.cards.view', $record)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('position')
            ->reorderable('position');
    }
}