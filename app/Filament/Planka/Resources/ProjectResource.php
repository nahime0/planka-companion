<?php

namespace App\Filament\Planka\Resources;

use App\Filament\Planka\Resources\ProjectResource\Pages;
use App\Filament\Planka\Resources\ProjectResource\RelationManagers;
use App\Models\Planka\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    
    protected static ?string $navigationLabel = 'Projects';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_hidden')
                            ->label('Hidden')
                            ->helperText('Hide this project from regular users'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Background')
                    ->schema([
                        Forms\Components\Select::make('background_type')
                            ->options([
                                'gradient' => 'Gradient',
                                'image' => 'Image',
                            ]),
                        Forms\Components\TextInput::make('background_gradient')
                            ->visible(fn ($get) => $get('background_type') === 'gradient'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('boards_count')
                    ->counts('boards')
                    ->label('Boards')
                    ->badge(),
                Tables\Columns\TextColumn::make('projectManagers_count')
                    ->counts('projectManagers')
                    ->label('Managers')
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_hidden')
                    ->boolean()
                    ->label('Hidden')
                    ->trueIcon('heroicon-o-eye-slash')
                    ->falseIcon('heroicon-o-eye'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_hidden')
                    ->label('Visibility')
                    ->placeholder('All projects')
                    ->trueLabel('Hidden only')
                    ->falseLabel('Visible only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BoardsRelationManager::class,
            RelationManagers\ProjectManagersRelationManager::class,
        ];
    }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('description'),
                        Infolists\Components\IconEntry::make('is_hidden')
                            ->label('Visibility')
                            ->boolean()
                            ->trueIcon('heroicon-o-eye-slash')
                            ->falseIcon('heroicon-o-eye')
                            ->trueColor('danger')
                            ->falseColor('success'),
                    ])
                    ->columns(1),
                    
                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('boards_count')
                            ->label('Total Boards')
                            ->state(fn ($record) => $record->boards()->count())
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('cards_count')
                            ->label('Total Cards')
                            ->state(fn ($record) => $record->boards()->withCount('cards')->get()->sum('cards_count'))
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('projectManagers_count')
                            ->label('Project Managers')
                            ->state(fn ($record) => $record->projectManagers()->count())
                            ->badge()
                            ->color('warning'),
                    ])
                    ->columns(3),
                    
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }
}
