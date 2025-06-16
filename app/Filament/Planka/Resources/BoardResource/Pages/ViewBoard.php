<?php

namespace App\Filament\Planka\Resources\BoardResource\Pages;

use App\Filament\Planka\Resources\BoardResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBoard extends ViewRecord
{
    protected static string $resource = BoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
