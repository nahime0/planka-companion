<?php

namespace App\Filament\Planka\Widgets;

use App\Models\Planka\Card;
use App\Models\Planka\CardMembership;
use App\Models\Planka\UserAccount;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class MyCardsWidget extends BaseWidget
{
    protected static ?string $heading = 'My Assigned Cards';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 2;
    
    protected static ?string $maxHeight = '400px';

    public function table(Table $table): Table
    {
        // For now, we'll show all cards as we don't have a logged-in Planka user context
        // In a real implementation, you'd filter by the current user's Planka ID
        
        return $table
            ->query(
                Card::query()
                    ->with(['board', 'list', 'labels', 'cardMemberships.user'])
                    ->whereHas('cardMemberships')
                    ->orderBy('due_date', 'asc')
                    ->orderBy('updated_at', 'desc')
                    ->limit(20)
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
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('list.name')
                    ->label('List')
                    ->limit(15)
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date('M j')
                    ->color(fn ($state) => 
                        $state ? (Carbon::parse($state)->isPast() ? 'danger' : 
                        (Carbon::parse($state)->diffInDays(now()) <= 3 ? 'warning' : null)) : null
                    ),
                Tables\Columns\TextColumn::make('labels.name')
                    ->label('Labels')
                    ->badge()
                    ->separator(', ')
                    ->limitList(2),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->state(function ($record) {
                        if (!$record->due_date) return 'Normal';
                        $daysUntilDue = Carbon::parse($record->due_date)->diffInDays(now(), false);
                        if ($daysUntilDue < 0) return 'Overdue';
                        if ($daysUntilDue <= 1) return 'Urgent';
                        if ($daysUntilDue <= 3) return 'High';
                        return 'Normal';
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Overdue' => 'danger',
                        'Urgent' => 'danger',
                        'High' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('No assigned cards')
            ->emptyStateDescription('You have no cards assigned to you.')
            ->emptyStateIcon('heroicon-o-inbox')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn ($record) => route('filament.planka.resources.cards.view', $record)),
            ]);
    }
}