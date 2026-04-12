<?php

namespace App\Filament\Resources\ShelfResource\Pages;

use App\Filament\Resources\ShelfResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShelf extends CreateRecord
{
    protected static string $resource = ShelfResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
