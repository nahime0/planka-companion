<?php

namespace App\Filament\Planka\Resources\BoardResource\Pages;

use App\Filament\Planka\Resources\BoardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoard extends EditRecord
{
    protected static string $resource = BoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
