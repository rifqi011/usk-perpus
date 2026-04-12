<?php

namespace App\Filament\Resources\MemberProfileResource\Pages;

use App\Enums\AccountType;
use App\Enums\MembershipStatus;
use App\Filament\Resources\MemberProfileResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateMemberProfile extends CreateRecord
{
    protected static string $resource = MemberProfileResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Member code sudah di-generate di form, tinggal pakai
        $memberCode = $data['member_code'];
        
        // Create user account first
        $user = User::create([
            'name' => $data['user']['name'],
            'email' => $data['user']['email'],
            'password' => Hash::make($data['user']['password']),
            'account_type' => AccountType::MEMBER,
            'status' => 'active',
        ]);
        
        // Prepare member profile data
        $memberData = [
            'user_id' => $user->id,
            'member_code' => $memberCode,
            'full_name' => $data['full_name'],
            'identity_number' => $data['identity_number'],
            'gender' => $data['gender'],
            'birth_place' => $data['birth_place'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'address' => $data['address'] ?? null,
            'photo' => $data['photo'] ?? null,
            'membership_status' => MembershipStatus::ACTIVE,
            'registration_date' => now(),
        ];
        
        return $memberData;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Anggota berhasil dibuat';
    }
}
