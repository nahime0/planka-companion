<?php

namespace App\Filament\Planka\Resources\UserAccountResource\Pages;

use App\Filament\Planka\Resources\UserAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserAccount extends ViewRecord
{
    protected static string $resource = UserAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
