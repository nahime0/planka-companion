<?php

namespace App\Filament\Planka\Resources\LabelResource\RelationManagers;

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
    
    protected static ?string $title = 'Cards Using This Label';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('board.name')
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
                    ->color('gray'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),
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
                Tables\Filters\SelectFilter::make('board')
                    ->relationship('board', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('list')
                    ->relationship('list', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => route('filament.planka.resources.cards.view', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->defaultSort('card.updated_at', 'desc');
    }
}