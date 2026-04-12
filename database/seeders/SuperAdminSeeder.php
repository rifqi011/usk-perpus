<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\AdminProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('code', 'superadmin')->first();

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@library.com',
            'password' => Hash::make('password'),
            'account_type' => AccountType::ADMIN->value,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Attach role
        $user->roles()->attach($superAdminRole->id);

        // Create admin profile
        AdminProfile::create([
            'user_id' => $user->id,
            'full_name' => 'Super Admin',
            'phone_number' => '081234567890',
            'position' => 'Super Administrator',
        ]);
    }
}
