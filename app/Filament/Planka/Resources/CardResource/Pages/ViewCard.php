<?php

namespace App\Filament\Planka\Resources\CardResource\Pages;

use App\Filament\Planka\Resources\CardResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCard extends ViewRecord
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
