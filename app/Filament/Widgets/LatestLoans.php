<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLoans extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Peminjaman Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::with(['member', 'details'])
                    ->latest('loan_date')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('loan_code')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('Anggota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                Tables\Columns\TextColumn::make('details_count')
                    ->label('Buku')
                    ->counts('details')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'info'    => 'borrowed',
                        'success' => 'returned',
                        'warning' => 'partially_returned',
                        'danger'  => 'overdue',
                    ])
                    ->formatStateUsing(fn ($state) => $state->label()),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.loans.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
