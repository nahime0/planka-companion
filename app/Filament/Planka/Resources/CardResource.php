<?php

namespace App\Filament\Planka\Resources;

use App\Filament\Planka\Resources\CardResource\Pages;
use App\Filament\Planka\Resources\CardResource\RelationManagers;
use App\Models\Planka\Card;
use App\Models\Planka\Board;
use App\Models\Planka\ListModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('board_id')
                            ->label('Board')
                            ->relationship('board', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('list_id', null)),
                        Forms\Components\Select::make('list_id')
                            ->label('List')
                            ->options(function (Forms\Get $get) {
                                $boardId = $get('board_id');
                                if (!$boardId) {
                                    return [];
                                }
                                return ListModel::where('board_id', $boardId)
                                    ->orderBy('position')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label('Card Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Due Date & Type')
                    ->schema([
                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Due Date')
                            ->native(false),
                    ]),
                Forms\Components\Section::make('Position')
                    ->schema([
                        Forms\Components\TextInput::make('position')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['board', 'list', 'labels', 'creatorUser'])
                ->withMax('comments', 'created_at')
                ->withMax('actions', 'created_at')
                ->selectRaw('*, GREATEST(
                    COALESCE(card.created_at, \'1970-01-01\'),
                    COALESCE(card.updated_at, \'1970-01-01\'),
                    COALESCE(card.list_changed_at, \'1970-01-01\'),
                    COALESCE((select max(created_at) from comment where comment.card_id = card.id), \'1970-01-01\'),
                    COALESCE((select max(created_at) from action where action.card_id = card.id), \'1970-01-01\')
                ) as last_activity')
            )
            ->columns([
                Tables\Columns\ViewColumn::make('hierarchy')
                    ->label('Location')
                    ->view('filament.planka.columns.card-hierarchy'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Card Name')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('labels.name')
                    ->label('Labels')
                    ->badge()
                    ->separator(', ')
                    ->limitList(3),
                Tables\Columns\TextColumn::make('cardMemberships_count')
                    ->counts('cardMemberships')
                    ->label('Members')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('comments_count')
                    ->counts('comments')
                    ->label('Comments')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('attachments_count')
                    ->counts('attachments')
                    ->label('Attachments')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($state): string => $state && Carbon::parse($state)->isPast() ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('board')
                    ->relationship('board', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('list')
                    ->relationship('list', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('has_due_date')
                    ->label('Has Due Date')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('due_date'),
                        false: fn (Builder $query) => $query->whereNull('due_date'),
                    ),
                Tables\Filters\TernaryFilter::make('overdue')
                    ->label('Overdue')
                    ->queries(
                        true: fn (Builder $query) => $query->where('due_date', '<', now()),
                        false: fn (Builder $query) => $query->where('due_date', '>=', now()),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->button()
                    ->size('xs')
                    ->color(Color::Green),
                Tables\Actions\Action::make('open_in_planka')
                    ->label('Planka')
                    ->button()
                    ->size('xs')
                    ->color(Color::Blue)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Card $record) => planka()->cardUrl($record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->defaultSort('last_activity', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\AttachmentsRelationManager::class,
            RelationManagers\CardMembershipsRelationManager::class,
            RelationManagers\LabelsRelationManager::class,
            RelationManagers\TaskListsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'view' => Pages\ViewCard::route('/{record}'),
        ];
    }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Card Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Card Name')
                            ->weight('bold')
                            ->size('lg'),
                        Infolists\Components\TextEntry::make('board.project.name')
                            ->label('Project'),
                        Infolists\Components\TextEntry::make('board.name')
                            ->label('Board'),
                        Infolists\Components\TextEntry::make('list.name')
                            ->label('List'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->html()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('due_date')
                            ->label('Due Date')
                            ->dateTime()
                            ->color(fn ($state): string => $state && Carbon::parse($state)->isPast() ? 'danger' : 'gray'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('comments_count')
                            ->label('Comments')
                            ->badge()
                            ->color('gray')
                            ->state(fn (Card $record): int => $record->comments()->count()),
                        Infolists\Components\TextEntry::make('attachments_count')
                            ->label('Attachments')
                            ->badge()
                            ->color('warning')
                            ->state(fn (Card $record): int => $record->attachments()->count()),
                        Infolists\Components\TextEntry::make('members_count')
                            ->label('Members')
                            ->badge()
                            ->color('info')
                            ->state(fn (Card $record): int => $record->cardMemberships()->count()),
                        Infolists\Components\TextEntry::make('tasks_count')
                            ->label('Tasks')
                            ->badge()
                            ->color('success')
                            ->state(fn (Card $record): int => $record->tasks()->count()),
                        Infolists\Components\TextEntry::make('labels')
                            ->label('Labels')
                            ->badge()
                            ->separator(', ')
                            ->getStateUsing(fn (Card $record): array => $record->labels->pluck('name')->toArray()),
                    ])
                    ->columns(5),
                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('creatorUser.name')
                            ->label('Created By'),
                    ])
                    ->columns(3),
            ]);
    }
}
