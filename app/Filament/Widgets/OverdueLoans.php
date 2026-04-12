<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueLoans extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()
                    ->with(['member.user'])
                    ->overdue()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('loan_code')
                    ->label('Kode Pinjaman'),
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('Anggota'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('late_days')
                    ->label('Hari Terlambat')
                    ->getStateUsing(fn ($record) => $record->calculateLateDays() . ' hari')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('member.user.email')
                    ->label('Email')
                    ->copyable(),
            ]);
    }
}
