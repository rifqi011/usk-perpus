<?php

namespace App\Filament\Resources\FineResource\Pages;

use App\Filament\Resources\FineResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListFines extends ListRecords
{
    protected static string $resource = FineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'unpaid' => Tab::make('Belum Dibayar')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'unpaid')),
            'paid' => Tab::make('Dibayar')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid')),
            'waived' => Tab::make('Dibebaskan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'waived')),
        ];
    }
}
