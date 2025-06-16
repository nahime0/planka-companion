<?php

namespace App\Filament\Planka\Resources;

use App\Filament\Planka\Resources\ListModelResource\Pages;
use App\Filament\Planka\Resources\ListModelResource\RelationManagers;
use App\Models\Planka\ListModel;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListModelResource extends Resource
{
    protected static ?string $model = ListModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    
    protected static ?string $navigationLabel = 'Lists';
    
    protected static ?string $label = 'List';
    
    protected static ?string $pluralLabel = 'Lists';
    
    protected static ?int $navigationSort = 3;

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
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'list' => 'List',
                                'archive' => 'Archive',
                            ])
                            ->default('list')
                            ->required(),
                        Forms\Components\ColorPicker::make('color'),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'list' => 'success',
                        'archive' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\TextColumn::make('position')
                    ->sortable()
                    ->numeric(decimalPlaces: 0)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cards_count')
                    ->counts('cards')
                    ->label('Cards')
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
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'list' => 'List',
                        'archive' => 'Archive',
                    ]),
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
                'type',
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
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('type')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'list' => 'success',
                                        'archive' => 'gray',
                                        default => 'gray',
                                    }),
                                Infolists\Components\ColorEntry::make('color')
                                    ->label('Color'),
                                Infolists\Components\TextEntry::make('position')
                                    ->numeric(decimalPlaces: 0),
                            ]),
                    ]),
                    
                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('cards_count')
                            ->label('Total Cards')
                            ->state(fn ($record) => $record->cards()->count())
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('active_cards')
                            ->label('Active Cards')
                            ->state(fn ($record) => $record->cards()->whereNull('due_date')->orWhere('due_date', '>', now())->count())
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('overdue_cards')
                            ->label('Overdue Cards')
                            ->state(fn ($record) => $record->cards()->where('due_date', '<', now())->count())
                            ->badge()
                            ->color('danger'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListModels::route('/'),
            'create' => Pages\CreateListModel::route('/create'),
            'view' => Pages\ViewListModel::route('/{record}'),
        ];
    }
}
