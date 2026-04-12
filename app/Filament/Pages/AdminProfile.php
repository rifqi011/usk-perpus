<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class AdminProfile extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.pages.admin-profile';
    
    protected static ?string $title = 'Profil Saya';
    
    protected static ?string $navigationLabel = 'Profil Saya';
    
    protected static ?string $navigationGroup = 'Manajemen User';
    
    protected static ?int $navigationSort = 99;
    
    // Hide from navigation, only accessible from user menu
    protected static bool $shouldRegisterNavigation = false;
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $user = auth()->user();
        
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'photo' => $user->adminProfile?->photo,
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->description('Update informasi akun Anda')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto Profil')
                            ->image()
                            ->avatar()
                            ->directory('admin-photos')
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Panggilan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->rule('unique:users,email,' . auth()->id())
                            ->maxLength(255),
                    ])->columns(2),
                
                Forms\Components\Section::make('Ubah Password')
                    ->description('Kosongkan jika tidak ingin mengubah password')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Password Saat Ini')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->rules(['required_with:new_password'])
                            ->helperText('Wajib diisi jika ingin mengubah password'),
                        Forms\Components\TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->dehydrated(false)
                            ->confirmed()
                            ->rules(['required_with:current_password']),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->dehydrated(false)
                            ->rules(['required_with:new_password']),
                    ])->columns(3),
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();
        
        // Validate current password if trying to change password
        if (!empty($data['current_password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->danger()
                    ->title('Gagal')
                    ->body('Password saat ini tidak sesuai.')
                    ->send();
                return;
            }
        }
        
        // Update user data
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];
        
        // Update password if provided
        if (!empty($data['new_password'])) {
            $userData['password'] = Hash::make($data['new_password']);
        }
        
        $user->update($userData);
        
        // Update admin profile photo
        if ($user->adminProfile) {
            $user->adminProfile->update([
                'photo' => $data['photo'] ?? null,
            ]);
        }
        
        Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Profil berhasil diperbarui.')
            ->send();
            
        // Refresh form with updated data
        $this->form->fill([
            'name' => $user->fresh()->name,
            'email' => $user->fresh()->email,
            'photo' => $user->fresh()->adminProfile?->photo,
        ]);
    }
    
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save')
                ->color('primary'),
        ];
    }
}
