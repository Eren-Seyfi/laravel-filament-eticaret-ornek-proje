<?php

namespace App\Filament\Resources\Adverts\Pages;

use App\Filament\Resources\Adverts\AdvertResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAdvert extends ViewRecord
{
    protected static string $resource = AdvertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
