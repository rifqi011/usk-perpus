<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Enums\AccountType;
use App\Filament\Resources\AdminResource;
use App\Models\Role;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;
    
    protected ?string $roleToAssign = null;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['account_type'] = AccountType::ADMIN;
        
        // Simpan role untuk diassign setelah user dibuat
        $this->roleToAssign = $data['role'] ?? 'admin';
        unset($data['role']);
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        // Assign role ke user
        $role = Role::where('name', $this->roleToAssign)->first();
        if ($role) {
            $this->record->roles()->sync([$role->id]);
        }
    }
}
