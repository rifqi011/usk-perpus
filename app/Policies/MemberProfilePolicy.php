<?php

namespace App\Policies;

use App\Models\MemberProfile;
use App\Models\User;

class MemberProfilePolicy
{
    /**
     * Member hanya bisa view profile sendiri
     */
    public function view(User $user, MemberProfile $memberProfile): bool
    {
        return $user->isMember() && $user->id === $memberProfile->user_id;
    }

    /**
     * Member hanya bisa update profile sendiri
     */
    public function update(User $user, MemberProfile $memberProfile): bool
    {
        return $user->isMember() && $user->id === $memberProfile->user_id;
    }

    /**
     * Admin bisa view semua member profiles
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
