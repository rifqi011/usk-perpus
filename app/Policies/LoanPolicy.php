<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    /**
     * Admin bisa view semua loans
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Member hanya bisa view loan sendiri
     * Admin bisa view semua loans
     */
    public function view(User $user, Loan $loan): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isMember()) {
            return $user->memberProfile && $user->memberProfile->id === $loan->member_id;
        }

        return false;
    }

    /**
     * Hanya admin yang bisa create loan
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang bisa process return
     */
    public function processReturn(User $user): bool
    {
        return $user->isAdmin();
    }
}
