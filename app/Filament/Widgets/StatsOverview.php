<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\MemberProfile;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Anggota Aktif', MemberProfile::active()->count())
                ->description('Anggota dengan status aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            
            Stat::make('Total Buku', Book::count())
                ->description(Book::sum('stock') . ' total eksemplar')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
            
            Stat::make('Copy Tersedia', Book::sum('available_stock'))
                ->description('Siap dipinjam')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Pinjaman Aktif', Loan::active()->count())
                ->description('Sedang dipinjam')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            
            Stat::make('Pinjaman Terlambat', Loan::overdue()->count())
                ->description('Melewati jatuh tempo')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            
            Stat::make('Denda Belum Dibayar', 'Rp ' . number_format(Fine::unpaid()->sum('amount'), 0, ',', '.'))
                ->description('Total outstanding')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('danger'),
        ];
    }
}
