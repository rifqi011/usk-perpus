<?php

namespace App\Filament\Resources;

use App\Actions\Member\ApproveRegistrationAction;
use App\Actions\Member\RejectRegistrationAction;
use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    
    protected static ?string $navigationGroup = 'Manajemen User';
    
    protected static ?string $navigationLabel = 'Pendaftaran';
    
    protected static ?string $modelLabel = 'Pendaftaran';
    
    protected static ?int $navigationSort = 3;
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : 'gray';
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
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identity_number')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => $state->label()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status->value === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pendaftaran')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pendaftaran ini? Akun member akan dibuat otomatis.')
                    ->action(function ($record, ApproveRegistrationAction $action) {
                        try {
                            $memberProfile = $action->execute($record, auth()->user());
                            
                            Notification::make()
                                ->success()
                                ->title('Pendaftaran Disetujui')
                                ->body("Member {$memberProfile->full_name} berhasil dibuat dengan kode {$memberProfile->member_code}")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status->value === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data, RejectRegistrationAction $action) {
                        try {
                            $action->execute($record, auth()->user(), $data['rejection_reason']);
                            
                            Notification::make()
                                ->success()
                                ->title('Pendaftaran Ditolak')
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
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Data Pendaftar')
                    ->schema([
                        Infolists\Components\TextEntry::make('full_name')
                            ->label('Nama Lengkap'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('phone_number')
                            ->label('No. Telepon'),
                        Infolists\Components\TextEntry::make('identity_number')
                            ->label('NIK'),
                        Infolists\Components\TextEntry::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Infolists\Components\Section::make('Status Pendaftaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match($state->value) {
                                'approved' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => $state->label()),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Daftar')
                            ->dateTime('d F Y H:i'),
                        Infolists\Components\TextEntry::make('approver.name')
                            ->label('Diproses Oleh')
                            ->visible(fn ($record) => $record->approved_by !== null),
                        Infolists\Components\TextEntry::make('approved_at')
                            ->label('Tanggal Diproses')
                            ->dateTime('d F Y H:i')
                            ->visible(fn ($record) => $record->approved_at !== null),
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->status->value === 'rejected'),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'view' => Pages\ViewRegistration::route('/{record}'),
        ];
    }
}
