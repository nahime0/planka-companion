<?php

namespace App\Filament\Planka\Widgets;

use App\Models\Planka\Card;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class OverdueCardsWidget extends BaseWidget
{
    protected static ?string $heading = 'Overdue Cards';
    
    protected static ?int $sort = 1;
    
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
                    ->where('due_date', '<', now())
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
                    ->date('M j, Y')
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('overdue_days')
                    ->label('Overdue')
                    ->state(function ($record) {
                        $diff = Carbon::parse($record->due_date)->diffForHumans(now(), [
                            'syntax' => Carbon::DIFF_ABSOLUTE,
                            'parts' => 1,
                            'options' => Carbon::ROUND,
                        ]);
                        return $diff;
                    })
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('members')
                    ->label('Assigned')
                    ->state(fn ($record) => $record->cardMemberships->pluck('user.name')->join(', '))
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->cardMemberships->pluck('user.name')->join(', ')),
            ])
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('No overdue cards')
            ->emptyStateDescription('Great! All cards are on track.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn ($record) => route('filament.planka.resources.cards.view', $record)),
            ]);
    }
}