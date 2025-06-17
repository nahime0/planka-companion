<?php

namespace App\Filament\Planka\Widgets;

use App\Filament\Planka\Utilities\CardActions;
use App\Models\Planka\Card;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class ExpiringCardsWidget extends BaseWidget
{
    protected static ?string $heading = 'Cards Expiring Soon';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = [
        'default' => 2,
        'sm' => 2,
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
        '2xl' => 1,
    ];
    
    protected static ?string $maxHeight = '400px';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Card::query()
                    ->with(['board', 'list', 'cardMemberships.user'])
                    ->whereNotNull('due_date')
                    ->whereBetween('due_date', [now(), now()->addDays(7)])
                    ->orderBy('due_date', 'asc')
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('bold')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->name)
                    ->url(fn ($record) => route('filament.planka.resources.cards.view', $record)),
                Tables\Columns\TextColumn::make('board.name')
                    ->label('Board')
                    ->limit(15)
                    ->tooltip(fn ($record) => $record->board->name),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->dateTime('M j, g:i A')
                    ->color('warning'),
                Tables\Columns\TextColumn::make('time_remaining')
                    ->label('Time Left')
                    ->state(function ($record) {
                        $diff = Carbon::parse($record->due_date)->diffForHumans(now(), [
                            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
                            'options' => Carbon::JUST_NOW | Carbon::ONE_DAY_WORDS | Carbon::TWO_DAY_WORDS,
                        ]);
                        return $diff;
                    })
                    ->badge()
                    ->color(fn ($record) => 
                        Carbon::parse($record->due_date)->diffInHours(now()) <= 24 ? 'danger' : 'warning'
                    ),
                Tables\Columns\TextColumn::make('members')
                    ->label('Assigned')
                    ->state(fn ($record) => $record->cardMemberships->pluck('user.name')->join(', '))
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->cardMemberships->pluck('user.name')->join(', ')),
            ])
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('No cards expiring soon')
            ->emptyStateDescription('No cards are due in the next 7 days.')
            ->emptyStateIcon('heroicon-o-clock')
            ->actions([
                CardActions::make(isWidget: true)
            ]);
    }
}