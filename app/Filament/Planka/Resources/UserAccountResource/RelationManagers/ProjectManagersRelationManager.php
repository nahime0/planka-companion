<?php

namespace App\Filament\Planka\Resources\UserAccountResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectManagersRelationManager extends RelationManager
{
    protected static string $relationship = 'projectManagers';
    
    protected static ?string $title = 'Managed Projects';
    
    protected static ?string $recordTitleAttribute = 'project.name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('project.name')
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('project.description')
                    ->label('Description')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('project.background_color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (string $state): string => $state ?? 'gray'),
                Tables\Columns\TextColumn::make('project.boards_count')
                    ->label('Boards')
                    ->counts('project.boards')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Assigned')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
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
            ->defaultSort('created_at', 'desc');
    }
}