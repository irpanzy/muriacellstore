<?php

namespace App\Filament\Resources\HandphoneResource\Pages;

use App\Filament\Resources\HandphoneResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHandphone extends EditRecord
{
    protected static string $resource = HandphoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
