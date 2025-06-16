<?php

namespace App\Filament\Planka\Resources\BoardResource\Pages;

use App\Filament\Planka\Resources\BoardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBoards extends ListRecords
{
    protected static string $resource = BoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
