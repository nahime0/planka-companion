<?php

namespace App\Filament\Planka\Widgets;

use App\Models\Planka\Project;
use App\Models\Planka\Board;
use App\Models\Planka\Card;
use App\Models\Planka\UserAccount;
use App\Models\Planka\Comment;
use App\Models\Planka\Attachment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PlankaStatsWidget extends BaseWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $totalBoards = Board::count();
        $totalCards = Card::count();
        $totalUsers = UserAccount::where('is_deactivated', false)->count();
        
        $overdueCards = Card::whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();
            
        $completedTasks = \App\Models\Planka\Task::where('is_completed', true)->count();
        $totalTasks = \App\Models\Planka\Task::count();
        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
        
        $activeCards = Card::where('created_at', '>=', now()->subDays(7))->count();
        $recentComments = Comment::where('created_at', '>=', now()->subDays(7))->count();
        $recentAttachments = Attachment::where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Total Projects', Number::format($totalProjects))
                ->description('Active projects')
                ->descriptionIcon('heroicon-m-folder')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url(route('filament.planka.resources.projects.index')),
                
            Stat::make('Total Boards', Number::format($totalBoards))
                ->description('Across all projects')
                ->descriptionIcon('heroicon-m-view-columns')
                ->color('success')
                ->chart([3, 15, 9, 3, 12, 8, 16])
                ->url(route('filament.planka.resources.boards.index')),
                
            Stat::make('Total Cards', Number::format($totalCards))
                ->description($overdueCards . ' overdue')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color($overdueCards > 0 ? 'warning' : 'info')
                ->chart([12, 8, 16, 12, 24, 18, 20])
                ->url(route('filament.planka.resources.cards.index')),
                
            Stat::make('Active Users', Number::format($totalUsers))
                ->description('Non-deactivated accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray')
                ->url(route('filament.planka.resources.user-accounts.index')),
                
            Stat::make('Task Completion', $taskCompletionRate . '%')
                ->description(Number::format($completedTasks) . ' of ' . Number::format($totalTasks) . ' tasks')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($taskCompletionRate >= 70 ? 'success' : ($taskCompletionRate >= 40 ? 'warning' : 'danger')),
                
            Stat::make('Weekly Activity', Number::format($activeCards + $recentComments + $recentAttachments))
                ->description($activeCards . ' cards, ' . $recentComments . ' comments')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([5, 10, 8, 12, 15, 18, 22]),
        ];
    }
}