<?php

namespace App\Filament\Planka\Resources\ListModelResource\Pages;

use App\Filament\Planka\Resources\ListModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListModels extends ListRecords
{
    protected static string $resource = ListModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
