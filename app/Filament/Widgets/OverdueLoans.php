<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueLoans extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Peminjaman Terlambat';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::with(['member', 'details'])
                    ->where(fn ($q) =>
                        $q->where('status', 'overdue')
                          ->orWhere(fn ($q2) =>
                              $q2->where('status', 'borrowed')
                                 ->where('due_date', '<', now())
                          )
                    )
                    ->orderBy('due_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('loan_code')
                    ->label('Kode'),
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('Anggota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.phone_number')
                    ->label('No. HP')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('late_days')
                    ->label('Hari Terlambat')
                    ->state(fn ($record) => now()->startOfDay()->diffInDays($record->due_date->startOfDay()) . ' hari')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('details_count')
                    ->label('Buku')
                    ->counts('details')
                    ->badge()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.loans.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('Tidak ada peminjaman terlambat')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated(false);
    }
}
