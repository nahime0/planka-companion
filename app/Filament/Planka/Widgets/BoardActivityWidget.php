<?php

namespace App\Filament\Planka\Widgets;

use App\Models\Planka\Card;
use App\Models\Planka\Comment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BoardActivityWidget extends ChartWidget
{
    protected static ?string $heading = 'Board Activity (Last 30 Days)';
    
    protected static ?int $sort = 0;
    
    protected int | string | array $columnSpan = 2;
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $days = collect(range(29, 0, -1))->map(function ($daysAgo) {
            $date = Carbon::now()->subDays($daysAgo);
            
            $cardsCreated = Card::whereDate('created_at', $date)->count();
            $cardsUpdated = Card::whereDate('updated_at', $date)
                ->whereColumn('updated_at', '!=', 'created_at')
                ->count();
            $comments = Comment::whereDate('created_at', $date)->count();
            
            return [
                'date' => $date->format('M j'),
                'cards' => $cardsCreated,
                'updates' => $cardsUpdated,
                'comments' => $comments,
                'total' => $cardsCreated + $cardsUpdated + $comments,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'New Cards',
                    'data' => $days->pluck('cards')->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                ],
                [
                    'label' => 'Card Updates',
                    'data' => $days->pluck('updates')->toArray(),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Comments',
                    'data' => $days->pluck('comments')->toArray(),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                ],
            ],
            'labels' => $days->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}