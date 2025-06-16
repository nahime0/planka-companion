<?php

namespace App\Filament\Planka\Widgets;

use App\Models\Planka\Board;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentBoardsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recently Updated Boards';
    
    protected static ?int $sort = 4;
    
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
                Board::query()
                    ->with(['project', 'lists', 'cards' => function ($query) {
                        $query->orderBy('updated_at', 'desc')->limit(1);
                    }])
                    ->withMax('cards', 'updated_at')
                    ->whereHas('cards')
                    ->orderBy('cards_max_updated_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold')
                    ->url(fn ($record) => route('filament.planka.resources.boards.view', $record)),
                Tables\Columns\TextColumn::make('project.name')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('lists_count')
                    ->counts('lists')
                    ->label('Lists')
                    ->badge(),
                Tables\Columns\TextColumn::make('cards_count')
                    ->counts('cards')
                    ->label('Cards')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('cards_max_updated_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->since()
                    ->placeholder('No activity'),
            ])
            ->paginated(false)
            ->searchable(false)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn ($record) => route('filament.planka.resources.boards.view', $record)),
            ]);
    }
}