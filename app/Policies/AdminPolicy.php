<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    /**
     * Hanya superadmin yang bisa manage admin lain
     */
    public function manageAdmins(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Hanya superadmin yang bisa manage roles
     */
    public function manageRoles(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Admin dan superadmin bisa approve registrasi
     */
    public function approveRegistration(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin dan superadmin bisa manage loans
     */
    public function manageLoans(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin dan superadmin bisa manage master data
     */
    public function manageMasterData(User $user): bool
    {
        return $user->isAdmin();
    }
}
