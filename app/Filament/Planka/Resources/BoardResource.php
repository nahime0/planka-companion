<?php

namespace App\Filament\Planka\Resources;

use App\Filament\Planka\Resources\BoardResource\Pages;
use App\Filament\Planka\Resources\BoardResource\RelationManagers;
use App\Models\Planka\Board;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoardResource extends Resource
{
    protected static ?string $model = Board::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    
    protected static ?string $navigationLabel = 'Boards';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('position')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('default_view')
                            ->options([
                                'board' => 'Board',
                                'list' => 'List',
                            ])
                            ->default('board'),
                        Forms\Components\Select::make('default_card_type')
                            ->options([
                                'simple' => 'Simple',
                                'detailed' => 'Detailed',
                            ])
                            ->default('simple'),
                        Forms\Components\Toggle::make('limit_card_types_to_default_one'),
                        Forms\Components\Toggle::make('always_display_card_creator'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('position')
                    ->sortable()
                    ->numeric(decimalPlaces: 0)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('lists_count')
                    ->counts('lists')
                    ->label('Lists')
                    ->badge(),
                Tables\Columns\TextColumn::make('cards_count')
                    ->counts('cards')
                    ->label('Cards')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('boardMemberships_count')
                    ->counts('boardMemberships')
                    ->label('Members')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('labels_count')
                    ->counts('labels')
                    ->label('Labels')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('default_view')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->defaultSort('project.name', 'asc')
            ->defaultSort('position', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ListsRelationManager::class,
            RelationManagers\LabelsRelationManager::class,
            RelationManagers\BoardMembershipsRelationManager::class,
        ];
    }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('project.name')
                            ->label('Project')
                            ->badge()
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('name')
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('default_view')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('default_card_type')
                                    ->badge(),
                            ]),
                    ]),
                    
                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('lists_count')
                            ->label('Total Lists')
                            ->state(fn ($record) => $record->lists()->count())
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('cards_count')
                            ->label('Total Cards')
                            ->state(fn ($record) => $record->cards()->count())
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('boardMemberships_count')
                            ->label('Members')
                            ->state(fn ($record) => $record->boardMemberships()->count())
                            ->badge()
                            ->color('warning'),
                        Infolists\Components\TextEntry::make('labels_count')
                            ->label('Labels')
                            ->state(fn ($record) => $record->labels()->count())
                            ->badge()
                            ->color('info'),
                    ])
                    ->columns(4),
                    
                Infolists\Components\Section::make('Settings')
                    ->schema([
                        Infolists\Components\IconEntry::make('limit_card_types_to_default_one')
                            ->boolean(),
                        Infolists\Components\IconEntry::make('always_display_card_creator')
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBoards::route('/'),
            'create' => Pages\CreateBoard::route('/create'),
            'view' => Pages\ViewBoard::route('/{record}'),
        ];
    }
}
