<?php

namespace App\Filament\Planka\Resources\CardResource\Pages;

use App\Filament\Planka\Resources\CardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCards extends ListRecords
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
