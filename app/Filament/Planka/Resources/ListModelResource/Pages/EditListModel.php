<?php

namespace App\Filament\Planka\Resources\ListModelResource\Pages;

use App\Filament\Planka\Resources\ListModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListModel extends EditRecord
{
    protected static string $resource = ListModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
