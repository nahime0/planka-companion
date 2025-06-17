<?php

namespace App\Filament\Planka\Utilities;

use App\Models\Planka\Card;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;

class CardActions
{
    /**
     * Get the standard action group for card tables
     * 
     * @param bool $isWidget Whether this is being used in a widget (true) or resource table (false)
     */
    public static function make(bool $isWidget = false): ActionGroup
    {
        $actions = [];
        
        // For widgets, use a custom action with URL; for resources, use ViewAction
        if ($isWidget) {
            $actions[] = Action::make('view')
                ->color(Color::Green)
                ->label('View Record')
                ->icon('heroicon-o-eye')
                ->url(fn (Card $record) => route('filament.planka.resources.cards.view', $record));
        } else {
            $actions[] = ViewAction::make()
                ->color(Color::Green)
                ->label('View Record');
        }
        
        // Open in Planka action is the same for both
        $actions[] = Action::make('open_in_planka')
            ->color(Color::Blue)
            ->label('Open in Planka')
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->url(fn (Card $record) => planka()->cardUrl($record))
            ->openUrlInNewTab();
        
        return ActionGroup::make($actions)
            ->button()
            ->size('xs')
            ->color(Color::Blue)
            ->label('Menu');
    }
}