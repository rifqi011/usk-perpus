<?php

namespace App\Filament\Resources\MemberProfileResource\Pages;

use App\Filament\Resources\MemberProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberProfile extends EditRecord
{
    protected static string $resource = MemberProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load user data for editing
        $data['user'] = [
            'name' => $this->record->user->name,
            'email' => $this->record->user->email,
        ];
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update user data
        $userData = [];
        
        if (isset($data['user']['name'])) {
            $userData['name'] = $data['user']['name'];
        }
        
        if (isset($data['user']['email']) && $data['user']['email'] !== $this->record->user->email) {
            $userData['email'] = $data['user']['email'];
        }
        
        // Update user password if provided
        if (isset($data['user']['password']) && filled($data['user']['password'])) {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($data['user']['password']);
        }
        
        if (!empty($userData)) {
            $this->record->user->update($userData);
        }
        
        // Remove user data from member profile data
        unset($data['user']);
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
