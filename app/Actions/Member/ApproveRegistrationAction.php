<?php

namespace App\Actions\Member;

use App\Enums\AccountType;
use App\Enums\MembershipStatus;
use App\Enums\RegistrationStatus;
use App\Models\MemberProfile;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApproveRegistrationAction
{
    public function execute(Registration $registration, User $approver): MemberProfile
    {
        if ($registration->status !== RegistrationStatus::PENDING) {
            throw new \Exception('Pendaftaran sudah diproses sebelumnya.');
        }

        return DB::transaction(function () use ($registration, $approver) {
            // Create user account
            $user = User::create([
                'name' => $registration->full_name,
                'email' => $registration->email,
                'password' => $registration->password, // Already hashed in registration
                'account_type' => AccountType::MEMBER,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            // Generate member code
            $memberCode = $this->generateMemberCode();

            // Create member profile
            $memberProfile = MemberProfile::create([
                'user_id' => $user->id,
                'member_code' => $memberCode,
                'identity_number' => $registration->identity_number,
                'full_name' => $registration->full_name,
                'gender' => 'L', // Default, bisa diupdate nanti
                'phone_number' => $registration->phone_number,
                'address' => $registration->address,
                'registration_date' => now(),
                'membership_status' => MembershipStatus::ACTIVE,
                'valid_until' => now()->addYear(), // Valid 1 tahun
            ]);

            // Update registration status
            $registration->update([
                'status' => RegistrationStatus::APPROVED,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);

            return $memberProfile;
        });
    }

    private function generateMemberCode(): string
    {
        $year = now()->year;
        $lastMember = MemberProfile::whereYear('registration_date', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastMember ? intval(substr($lastMember->member_code, -4)) + 1 : 1;

        return sprintf('MBR%s%04d', $year, $sequence);
    }
}
