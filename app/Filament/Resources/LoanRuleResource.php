<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanRuleResource\Pages;
use App\Models\LoanRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoanRuleResource extends Resource
{
    protected static ?string $model = LoanRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Pengaturan';
    
    protected static ?string $navigationLabel = 'Aturan Peminjaman';
    
    protected static ?string $modelLabel = 'Aturan Peminjaman';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Aturan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Kode unik untuk aturan ini'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(3),
                
                Forms\Components\Section::make('Aturan Peminjaman')
                    ->schema([
                        Forms\Components\TextInput::make('max_active_loans')
                            ->label('Maksimal Pinjaman Aktif')
                            ->numeric()
                            ->required()
                            ->default(3)
                            ->minValue(1),
                        Forms\Components\TextInput::make('max_loan_days')
                            ->label('Maksimal Hari Pinjam')
                            ->numeric()
                            ->required()
                            ->default(7)
                            ->minValue(1)
                            ->suffix('hari'),
                        Forms\Components\TextInput::make('grace_days')
                            ->label('Masa Tenggang')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('hari')
                            ->helperText('Hari bebas denda setelah jatuh tempo'),
                        Forms\Components\Toggle::make('can_renew')
                            ->label('Dapat Diperpanjang')
                            ->default(true)
                            ->live(),
                        Forms\Components\TextInput::make('max_renew_count')
                            ->label('Maksimal Perpanjangan')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(0)
                            ->visible(fn ($get) => $get('can_renew')),
                    ])->columns(3),
                
                Forms\Components\Section::make('Aturan Denda')
                    ->schema([
                        Forms\Components\TextInput::make('fine_per_day')
                            ->label('Denda Per Hari')
                            ->numeric()
                            ->required()
                            ->default(1000)
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('damage_fine_minor')
                            ->label('Denda Kerusakan Ringan')
                            ->numeric()
                            ->required()
                            ->default(10000)
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('damage_fine_major')
                            ->label('Denda Kerusakan Berat')
                            ->numeric()
                            ->required()
                            ->default(50000)
                            ->prefix('Rp'),
                        Forms\Components\Select::make('lost_book_fine_type')
                            ->label('Tipe Denda Buku Hilang')
                            ->options([
                                'fixed' => 'Nominal Tetap',
                                'book_price' => 'Harga Buku',
                            ])
                            ->required()
                            ->default('book_price')
                            ->native(false)
                            ->live(),
                        Forms\Components\TextInput::make('lost_book_fine_amount')
                            ->label('Nominal Denda Buku Hilang')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('Rp')
                            ->visible(fn ($get) => $get('lost_book_fine_type') === 'fixed'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Aturan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('max_active_loans')
                    ->label('Maks. Pinjaman')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('max_loan_days')
                    ->label('Maks. Hari')
                    ->sortable()
                    ->alignCenter()
                    ->suffix(' hari'),
                Tables\Columns\TextColumn::make('fine_per_day')
                    ->label('Denda/Hari')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_renew')
                    ->label('Perpanjangan')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
                Tables\Filters\TernaryFilter::make('can_renew')
                    ->label('Perpanjangan')
                    ->placeholder('Semua')
                    ->trueLabel('Dapat Diperpanjang')
                    ->falseLabel('Tidak Dapat Diperpanjang'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoanRules::route('/'),
            'create' => Pages\CreateLoanRule::route('/create'),
            'edit' => Pages\EditLoanRule::route('/{record}/edit'),
        ];
    }
}
