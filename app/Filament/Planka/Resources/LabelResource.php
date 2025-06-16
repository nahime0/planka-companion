<?php

namespace App\Filament\Planka\Resources;

use App\Filament\Planka\Resources\LabelResource\Pages;
use App\Filament\Planka\Resources\LabelResource\RelationManagers;
use App\Models\Planka\Label;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LabelResource extends Resource
{
    protected static ?string $model = Label::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationLabel = 'Labels';
    
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('board_id')
                            ->relationship('board', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255)
                            ->placeholder('Label name (optional)'),
                        Forms\Components\ColorPicker::make('color')
                            ->required(),
                        Forms\Components\TextInput::make('position')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('board.project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('board.name')
                    ->label('Board')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\ViewColumn::make('label_preview')
                    ->label('Label')
                    ->view('filament.planka.columns.label-preview'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No name')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ColorColumn::make('color')
                    ->copyable(),
                Tables\Columns\TextColumn::make('position')
                    ->sortable()
                    ->numeric(decimalPlaces: 0)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('board')
                    ->relationship('board', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->defaultSort('board.name')
            ->groups([
                'board.name',
                'board.project.name',
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CardsRelationManager::class,
        ];
    }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('board.project.name')
                            ->label('Project')
                            ->badge()
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('board.name')
                            ->label('Board')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Label Name')
                            ->placeholder('No name')
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\ColorEntry::make('color')
                            ->label('Color'),
                        Infolists\Components\TextEntry::make('position')
                            ->numeric(decimalPlaces: 0),
                    ]),
                    
                Infolists\Components\Section::make('Usage Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('cards_count')
                            ->label('Used in Cards')
                            ->state(fn ($record) => $record->cards()->count())
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('unique_lists')
                            ->label('Appears in Lists')
                            ->state(fn ($record) => $record->cards()->distinct('list_id')->count('list_id'))
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabels::route('/'),
            'create' => Pages\CreateLabel::route('/create'),
            'view' => Pages\ViewLabel::route('/{record}'),
        ];
    }
}
