<?php

namespace App\Filament\Planka\Resources\BoardResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LabelsRelationManager extends RelationManager
{
    protected static string $relationship = 'labels';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
                Forms\Components\ColorPicker::make('color')
                    ->required(),
                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No name')
                    ->weight('bold'),
                Tables\Columns\ColorColumn::make('color')
                    ->copyable(),
                Tables\Columns\TextColumn::make('position')
                    ->sortable()
                    ->numeric(decimalPlaces: 0),
                Tables\Columns\TextColumn::make('cards_count')
                    ->label('Used in Cards')
                    ->state(fn ($record) => $record->cards()->count())
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
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