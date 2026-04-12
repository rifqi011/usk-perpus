<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'code' => 'superadmin',
                'description' => 'Super administrator dengan akses penuh ke seluruh sistem',
            ],
            [
                'name' => 'Admin',
                'code' => 'admin',
                'description' => 'Administrator dengan akses terbatas',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
