<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use App\Models\Role;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;
    
    protected ?string $roleToAssign = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load role saat ini untuk ditampilkan di form
        $currentRole = $this->record->roles()->first();
        $data['role'] = $currentRole?->name ?? 'admin';
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Simpan role untuk diassign setelah user diupdate
        $this->roleToAssign = $data['role'] ?? 'admin';
        unset($data['role']);
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Update role user
        $role = Role::where('name', $this->roleToAssign)->first();
        if ($role) {
            $this->record->roles()->sync([$role->id]);
        }
    }
}
