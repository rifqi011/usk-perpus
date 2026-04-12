<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberProfileResource\Pages;
use App\Models\MemberProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class MemberProfileResource extends Resource
{
    protected static ?string $model = MemberProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Manajemen User';
    
    protected static ?string $navigationLabel = 'Anggota';
    
    protected static ?string $modelLabel = 'Anggota';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->description('Data akun untuk login anggota')
                    ->schema([
                        Forms\Components\TextInput::make('member_code')
                            ->label('Kode Anggota')
                            ->default(function () {
                                $date = date('dmy');
                                $randomNumber = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                                return 'M-' . $date . '-' . $randomNumber;
                            })
                            ->readOnly()
                            ->dehydrated()
                            ->helperText('Kode anggota dibuat otomatis')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Nama Akun')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nama yang akan ditampilkan di sistem'),
                        Forms\Components\TextInput::make('user.email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('user.password')
                            ->label('Password')
                            ->password()
                            ->required(fn ($context) => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8)
                            ->maxLength(255)
                            ->confirmed()
                            ->helperText(fn ($context) => $context === 'edit' ? 'Kosongkan jika tidak ingin mengubah password' : null),
                        Forms\Components\TextInput::make('user.password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->required(fn ($context) => $context === 'create')
                            ->dehydrated(false)
                            ->minLength(8)
                            ->maxLength(255)
                            ->visible(fn ($context) => $context === 'create'),
                        Forms\Components\Select::make('membership_status')
                            ->label('Status Keanggotaan')
                            ->options([
                                'active' => 'Aktif',
                                'suspended' => 'Suspended',
                                'inactive' => 'Tidak Aktif',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false)
                            ->visible(fn ($context) => $context === 'edit'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Data Pribadi')
                    ->description('Data identitas anggota')
                    ->schema([
                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('identity_number')
                            ->label('NIK')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->maxDate(now()->subYears(5)),
                        Forms\Components\TextInput::make('phone_number')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto')
                            ->image()
                            ->directory('members')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('member_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('membership_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger' => 'suspended',
                        'gray' => 'inactive',
                    ])
                    ->formatStateUsing(fn ($state) => $state->label()),
                Tables\Columns\TextColumn::make('registration_date')
                    ->label('Tgl Daftar')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record) => $record->membership_status->value === 'active')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['membership_status' => 'suspended'])),
                Tables\Actions\Action::make('activate')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->membership_status->value !== 'active')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['membership_status' => 'active'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Anggota')
                    ->schema([
                        Infolists\Components\ImageEntry::make('photo')
                            ->label('Foto')
                            ->circular(),
                        Infolists\Components\TextEntry::make('member_code')
                            ->label('Kode Anggota'),
                        Infolists\Components\TextEntry::make('full_name')
                            ->label('Nama Lengkap'),
                        Infolists\Components\TextEntry::make('identity_number')
                            ->label('NIK'),
                        Infolists\Components\TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn ($state) => $state->label()),
                        Infolists\Components\TextEntry::make('birth_place')
                            ->label('Tempat Lahir'),
                        Infolists\Components\TextEntry::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->date('d F Y'),
                        Infolists\Components\TextEntry::make('phone_number')
                            ->label('No. Telepon'),
                        Infolists\Components\TextEntry::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Infolists\Components\Section::make('Status Keanggotaan')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('membership_status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match($state->value) {
                                'active' => 'success',
                                'pending' => 'warning',
                                'suspended' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => $state->label()),
                        Infolists\Components\TextEntry::make('registration_date')
                            ->label('Tanggal Daftar')
                            ->date('d F Y'),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemberProfiles::route('/'),
            'create' => Pages\CreateMemberProfile::route('/create'),
            'view' => Pages\ViewMemberProfile::route('/{record}'),
            'edit' => Pages\EditMemberProfile::route('/{record}/edit'),
        ];
    }
}
