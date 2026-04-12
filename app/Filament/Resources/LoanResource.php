<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\LoanRule;
use App\Models\MemberProfile;
use App\Services\LoanService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    
    protected static ?string $navigationGroup = 'Transaksi';
    
    protected static ?string $navigationLabel = 'Peminjaman';
    
    protected static ?string $modelLabel = 'Peminjaman';
    
    protected static ?int $navigationSort = 1;
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peminjaman')
                    ->schema([
                        Forms\Components\Select::make('member_id')
                            ->label('Anggota')
                            ->options(MemberProfile::active()->get()->pluck('full_name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('loan_rule_id')
                            ->label('Aturan Peminjaman')
                            ->options(LoanRule::active()->pluck('name', 'id'))
                            ->default(fn () => LoanRule::active()->first()?->id)
                            ->required()
                            ->native(false)
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('loan_date')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->columnSpan(1),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Buku yang Dipinjam')
                    ->schema([
                        Forms\Components\Repeater::make('book_copies')
                            ->label(false)
                            ->schema([
                                Forms\Components\Select::make('book_id')
                                    ->label('Buku')
                                    ->options(function () {
                                        return Book::active()
                                            ->where('available_stock', '>', 0)
                                            ->get()
                                            ->mapWithKeys(fn ($book) => [
                                                $book->id => "{$book->title} (Stok: {$book->available_stock})"
                                            ]);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->native(false)
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Tambah Buku')
                            ->maxItems(10)
                            ->collapsible()
                            ->deleteAction(
                                fn (Forms\Components\Actions\Action $action) => 
                                    $action->hidden(fn (Forms\Get $get): bool => count($get('book_copies') ?? []) <= 1)
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('Anggota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'info' => 'borrowed',
                        'success' => 'returned',
                        'warning' => 'partially_returned',
                        'danger' => 'overdue',
                    ])
                    ->formatStateUsing(fn ($state) => $state->label()),
                Tables\Columns\TextColumn::make('total_fine')
                    ->label('Total Denda')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn ($record) => $record->isActive())
                    ->form(function ($record) {
                        $unreturned = $record->details()
                            ->where('status', '!=', 'returned')
                            ->with('book')
                            ->get();
                        
                        return [
                            Forms\Components\Repeater::make('returns')
                                ->label('Buku yang Dikembalikan')
                                ->schema([
                                    Forms\Components\Select::make('loan_detail_id')
                                        ->label('Buku')
                                        ->options($unreturned->mapWithKeys(fn ($detail) => [
                                            $detail->id => $detail->book->title
                                        ]))
                                        ->required()
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->native(false),
                                    Forms\Components\Textarea::make('notes')
                                        ->label('Catatan')
                                        ->rows(2),
                                ])
                                ->columns(2)
                                ->defaultItems(1)
                                ->addActionLabel('Tambah Buku')
                                ->required(),
                        ];
                    })
                    ->action(function ($record, array $data, LoanService $loanService) {
                        try {
                            $returnData = collect($data['returns'])
                                ->mapWithKeys(fn ($item) => [
                                    $item['loan_detail_id'] => [
                                        'condition' => $item['condition'],
                                        'notes' => $item['notes'] ?? null,
                                    ]
                                ])
                                ->toArray();
                            
                            $loan = $loanService->processReturn($record, $returnData);
                            
                            Notification::make()
                                ->success()
                                ->title('Pengembalian Berhasil')
                                ->body("Total denda: Rp " . number_format($loan->total_fine, 0, ',', '.'))
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('loan_date', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Peminjaman')
                    ->schema([
                        Infolists\Components\TextEntry::make('loan_code')
                            ->label('Kode Peminjaman'),
                        Infolists\Components\TextEntry::make('member.full_name')
                            ->label('Anggota'),
                        Infolists\Components\TextEntry::make('member.member_code')
                            ->label('Kode Anggota'),
                        Infolists\Components\TextEntry::make('loan_date')
                            ->label('Tanggal Pinjam')
                            ->date('d F Y'),
                        Infolists\Components\TextEntry::make('due_date')
                            ->label('Jatuh Tempo')
                            ->date('d F Y')
                            ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                        Infolists\Components\TextEntry::make('return_date')
                            ->label('Tanggal Kembali')
                            ->date('d F Y')
                            ->visible(fn ($record) => $record->return_date !== null),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match($state->value) {
                                'borrowed' => 'info',
                                'returned' => 'success',
                                'partially_returned' => 'warning',
                                'overdue' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => $state->label()),
                        Infolists\Components\TextEntry::make('total_late_days')
                            ->label('Hari Terlambat')
                            ->suffix(' hari')
                            ->visible(fn ($record) => $record->total_late_days > 0),
                        Infolists\Components\TextEntry::make('total_fine')
                            ->label('Total Denda')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->notes !== null),
                    ])->columns(3),
                
                Infolists\Components\Section::make('Detail Buku')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('details')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('book.title')
                                    ->label('Judul Buku'),
                                Infolists\Components\TextEntry::make('book.sku')
                                    ->label('SKU'),
                                Infolists\Components\TextEntry::make('due_date')
                                    ->label('Jatuh Tempo')
                                    ->date('d F Y'),
                                Infolists\Components\TextEntry::make('returned_at')
                                    ->label('Dikembalikan')
                                    ->dateTime('d F Y H:i')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('late_days')
                                    ->label('Hari Terlambat')
                                    ->suffix(' hari'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state->label()),
                                Infolists\Components\TextEntry::make('fine_amount')
                                    ->label('Denda')
                                    ->money('IDR'),
                            ])
                            ->columns(3),
                    ]),
                
                Infolists\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Infolists\Components\TextEntry::make('creator.name')
                            ->label('Dibuat Oleh'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d F Y H:i'),
                        Infolists\Components\TextEntry::make('returner.name')
                            ->label('Dikembalikan Oleh')
                            ->visible(fn ($record) => $record->returned_by !== null),
                        Infolists\Components\TextEntry::make('return_processed_at')
                            ->label('Diproses Pada')
                            ->dateTime('d F Y H:i')
                            ->visible(fn ($record) => $record->return_processed_at !== null),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
        ];
    }
}
