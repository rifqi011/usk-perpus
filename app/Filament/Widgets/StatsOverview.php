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
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeLoans    = Loan::active()->count();
        $overdueLoans   = Loan::where('status', 'overdue')
            ->orWhere(fn ($q) => $q->where('status', 'borrowed')->where('due_date', '<', now()))
            ->count();
        $totalMembers   = MemberProfile::active()->count();
        $totalBooks     = Book::active()->count();
        $availableBooks = Book::active()->where('available_stock', '>', 0)->count();
        $unpaidFines    = Fine::unpaid()->sum('amount');

        return [
            Stat::make('Peminjaman Aktif', $activeLoans)
                ->description('Sedang dipinjam')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->icon('heroicon-o-arrow-path'),

            Stat::make('Terlambat', $overdueLoans)
                ->description('Melewati jatuh tempo')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueLoans > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-clock'),

            Stat::make('Anggota Aktif', $totalMembers)
                ->description('Terdaftar & aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make('Total Buku', $totalBooks)
                ->description("{$availableBooks} judul tersedia")
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning')
                ->icon('heroicon-o-book-open'),

            Stat::make('Denda Belum Dibayar', 'Rp ' . number_format($unpaidFines, 0, ',', '.'))
                ->description('Total denda outstanding')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($unpaidFines > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
