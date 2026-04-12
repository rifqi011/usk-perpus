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
            'active' => Tab::make('Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->active()),
            'borrowed' => Tab::make('Dipinjam')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'borrowed')),
            'partially_returned' => Tab::make('Sebagian Kembali')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'partially_returned')),
            'overdue' => Tab::make('Terlambat')
                ->modifyQueryUsing(fn (Builder $query) => $query->overdue()),
            'returned' => Tab::make('Dikembalikan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'returned')),
        ];
    }
}
