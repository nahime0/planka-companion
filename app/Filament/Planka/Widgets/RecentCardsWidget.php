<?php

namespace App\Filament\Planka\Widgets;

use App\Models\Planka\Card;
use App\Filament\Planka\Resources\CardResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class RecentCardsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recently Updated Cards';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '400px';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Card::query()
                    ->with(['board', 'list', 'labels', 'creatorUser'])
                    ->withMax('comments', 'created_at')
                    ->withMax('actions', 'created_at')
                    ->orderByRaw('GREATEST(
                        COALESCE(card.created_at, \'1970-01-01\'),
                        COALESCE(card.updated_at, \'1970-01-01\'),
                        COALESCE(card.list_changed_at, \'1970-01-01\'),
                        COALESCE((select max(created_at) from comment where comment.card_id = card.id), \'1970-01-01\'),
                        COALESCE((select max(created_at) from action where action.card_id = card.id), \'1970-01-01\')
                    ) DESC')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->name)
                    ->url(fn ($record) => route('filament.planka.resources.cards.view', $record)),
                Tables\Columns\TextColumn::make('board.name')
                    ->label('Board')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('list.name')
                    ->label('List')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('labels.name')
                    ->label('Labels')
                    ->badge()
                    ->separator(', ')
                    ->limitList(2),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->dateTime('M j')
                    ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Last Activity')
                    ->state(function ($record) {
                        $dates = [
                            $record->created_at,
                            $record->updated_at,
                            $record->list_changed_at,
                            $record->comments_max_created_at,
                            $record->actions_max_created_at,
                        ];
                        
                        $mostRecent = collect($dates)
                            ->filter()
                            ->max();
                            
                        return $mostRecent ? Carbon::parse($mostRecent) : null;
                    })
                    ->since()
                    ->placeholder('No activity'),
            ])
            ->paginated(false)
            ->searchable(false)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn ($record) => route('filament.planka.resources.cards.view', $record)),
            ])
            ->headerActions([
                Tables\Actions\Action::make('show_all')
                    ->label('Show All')
                    ->button()
                    ->icon('heroicon-o-arrow-right')
                    ->url(CardResource::getUrl('index'))
                    ->size('sm'),
            ]);
    }
}