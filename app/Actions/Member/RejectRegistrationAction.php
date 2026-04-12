<?php

namespace App\Actions\Member;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\User;

class RejectRegistrationAction
{
    public function execute(Registration $registration, User $approver, string $reason): Registration
    {
        if ($registration->status !== RegistrationStatus::PENDING) {
            throw new \Exception('Pendaftaran sudah diproses sebelumnya.');
        }

        $registration->update([
            'status' => RegistrationStatus::REJECTED,
            'rejection_reason' => $reason,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        return $registration;
    }
}
