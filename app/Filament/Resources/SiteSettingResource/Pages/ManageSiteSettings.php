<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ManageSiteSettings extends Page
{
    protected static string $resource = SiteSettingResource::class;

    protected static string $view = 'filament.pages.manage-site-settings';
    
    protected static ?string $title = 'Pengaturan Situs';
    
    public ?array $data = [];
    
    public bool $isEditing = false;

    public function mount(): void
    {
        $setting = SiteSetting::getInstance();
        $this->form->fill($setting->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(SiteSettingResource::getFormSchema())
            ->statePath('data');
    }

    public function toggleEdit(): void
    {
        $this->isEditing = !$this->isEditing;
        
        if (!$this->isEditing) {
            // Cancel - reload data
            $setting = SiteSetting::getInstance();
            $this->form->fill($setting->toArray());
        }
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $setting = SiteSetting::getInstance();
        $setting->update($data);
        
        $this->isEditing = false;
        
        Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Pengaturan situs berhasil disimpan.')
            ->send();
    }
    
    protected function getHeaderActions(): array
    {
        if (!$this->isEditing) {
            return [
                \Filament\Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->action('toggleEdit'),
            ];
        }
        
        return [
            \Filament\Actions\Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->action('toggleEdit'),
            \Filament\Actions\Action::make('save')
                ->label('Simpan')
                ->icon('heroicon-o-check')
                ->action('save'),
        ];
    }
}
