<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLoans extends ListRecords
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'borrowed' => Tab::make('Dipinjam')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'borrowed')),
            'overdue' => Tab::make('Terlambat')
                ->modifyQueryUsing(fn (Builder $query) => $query->overdue()),
            'returned' => Tab::make('Dikembalikan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'returned')),
        ];
    }
}
