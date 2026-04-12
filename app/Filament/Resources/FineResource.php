<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FineResource\Pages;
use App\Models\Fine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FineResource extends Resource
{
    protected static ?string $model = Fine::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationGroup = 'Transaksi';
    
    protected static ?string $navigationLabel = 'Denda';
    
    protected static ?string $modelLabel = 'Denda';
    
    protected static ?int $navigationSort = 2;
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::unpaid()->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loanDetail.loan.loan_code')
                    ->label('Kode Pinjaman')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('Anggota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('loanDetail.bookCopy.book.title')
                    ->label('Buku')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('fine_type')
                    ->label('Jenis Denda')
                    ->formatStateUsing(fn ($state) => $state->label()),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'unpaid',
                        'success' => 'paid',
                        'gray' => 'waived',
                    ])
                    ->formatStateUsing(fn ($state) => $state->label()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fine_type')
                    ->label('Jenis Denda')
                    ->options([
                        'late_return' => 'Keterlambatan',
                        'minor_damage' => 'Kerusakan Ringan',
                        'major_damage' => 'Kerusakan Berat',
                        'lost_book' => 'Buku Hilang',
                        'other' => 'Lainnya',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->label('Bayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status->value === 'unpaid')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->markAsPaid();
                        
                        Notification::make()
                            ->success()
                            ->title('Denda Dibayar')
                            ->send();
                    }),
                Tables\Actions\Action::make('waive')
                    ->label('Bebaskan')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status->value === 'unpaid')
                    ->requiresConfirmation()
                    ->modalHeading('Bebaskan Denda')
                    ->modalDescription('Apakah Anda yakin ingin membebaskan denda ini?')
                    ->action(function ($record) {
                        $record->waive();
                        
                        Notification::make()
                            ->success()
                            ->title('Denda Dibebaskan')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('pay_selected')
                    ->label('Bayar Terpilih')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $records->each->markAsPaid();
                        
                        Notification::make()
                            ->success()
                            ->title('Denda Dibayar')
                            ->body("{$records->count()} denda berhasil dibayar")
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFines::route('/'),
        ];
    }
}
