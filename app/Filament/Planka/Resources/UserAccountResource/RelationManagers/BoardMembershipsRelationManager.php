<?php

namespace App\Filament\Planka\Resources\UserAccountResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoardMembershipsRelationManager extends RelationManager
{
    protected static string $relationship = 'boardMemberships';
    
    protected static ?string $title = 'Board Memberships';
    
    protected static ?string $recordTitleAttribute = 'board.name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('board_id')
                    ->relationship('board', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('role')
                    ->options([
                        'editor' => 'Editor',
                        'viewer' => 'Viewer',
                    ])
                    ->required()
                    ->default('editor'),
                Forms\Components\Toggle::make('can_comment')
                    ->label('Can Comment')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('board.name')
            ->columns([
                Tables\Columns\TextColumn::make('board.name')
                    ->label('Board Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('board.project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'editor' => 'success',
                        'viewer' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('can_comment')
                    ->label('Can Comment')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('lists_count')
                    ->label('Lists')
                    ->state(fn ($record) => $record->board->lists()->count())
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'editor' => 'Editor',
                        'viewer' => 'Viewer',
                    ]),
                Tables\Filters\TernaryFilter::make('can_comment')
                    ->label('Can Comment'),
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
            ->defaultSort('created_at', 'desc');
    }
}