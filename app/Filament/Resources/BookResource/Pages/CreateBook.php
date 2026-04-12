<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['stock'] = $data['initial_stock'] ?? 0;
        $data['available_stock'] = $data['initial_stock'] ?? 0;
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
