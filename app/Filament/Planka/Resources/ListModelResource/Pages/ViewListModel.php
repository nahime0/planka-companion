<?php

namespace App\Filament\Planka\Resources\ListModelResource\Pages;

use App\Filament\Planka\Resources\ListModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewListModel extends ViewRecord
{
    protected static string $resource = ListModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
