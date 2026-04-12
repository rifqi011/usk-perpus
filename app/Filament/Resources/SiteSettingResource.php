<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    
    protected static ?string $navigationGroup = 'Pengaturan';
    
    protected static ?string $navigationLabel = 'Pengaturan Situs';
    
    protected static ?string $modelLabel = 'Pengaturan Situs';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getFormSchema());
    }
    
    public static function getFormSchema(): array
    {
        return [
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informasi Umum')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('site_name')
                                    ->label('Nama Perpustakaan')
                                    ->required()
                                    ->maxLength(255)
                                    ->default('Perpustakaan'),
                                Forms\Components\TextInput::make('site_tagline')
                                    ->label('Tagline')
                                    ->maxLength(255)
                                    ->placeholder('Membaca adalah jendela dunia'),
                                Forms\Components\Textarea::make('site_description')
                                    ->label('Deskripsi')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),
                        
                        Forms\Components\Tabs\Tab::make('Branding')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('site_logo')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('branding')
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->helperText('Ukuran maksimal 2MB. Format: JPG, PNG'),
                                Forms\Components\FileUpload::make('site_favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->directory('branding')
                                    ->maxSize(512)
                                    ->helperText('Ukuran maksimal 512KB. Format: ICO, PNG (32x32 atau 64x64)'),
                            ])->columns(2),
                        
                        Forms\Components\Tabs\Tab::make('Kontak')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Telepon')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('contact_address')
                                    ->label('Alamat')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),
                        
                        Forms\Components\Tabs\Tab::make('Media Sosial')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('facebook_url')
                                    ->label('Facebook')
                                    ->url()
                                    ->maxLength(255)
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('twitter_url')
                                    ->label('Twitter/X')
                                    ->url()
                                    ->maxLength(255)
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('instagram_url')
                                    ->label('Instagram')
                                    ->url()
                                    ->maxLength(255)
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('youtube_url')
                                    ->label('YouTube')
                                    ->url()
                                    ->maxLength(255)
                                    ->prefix('https://'),
                            ])->columns(2),
                        
                        Forms\Components\Tabs\Tab::make('Jam Operasional')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Forms\Components\TimePicker::make('opening_time')
                                    ->label('Jam Buka')
                                    ->seconds(false),
                                Forms\Components\TimePicker::make('closing_time')
                                    ->label('Jam Tutup')
                                    ->seconds(false),
                                Forms\Components\CheckboxList::make('opening_days')
                                    ->label('Hari Buka')
                                    ->options([
                                        'monday' => 'Senin',
                                        'tuesday' => 'Selasa',
                                        'wednesday' => 'Rabu',
                                        'thursday' => 'Kamis',
                                        'friday' => 'Jumat',
                                        'saturday' => 'Sabtu',
                                        'sunday' => 'Minggu',
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSiteSettings::route('/'),
        ];
    }
}
