<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::create([
            'site_name' => 'Perpustakaan',
            'site_tagline' => 'Membaca adalah jendela dunia',
            'site_description' => 'Perpustakaan modern dengan koleksi buku lengkap dan sistem peminjaman yang mudah.',
            'contact_email' => 'info@perpustakaan.com',
            'contact_phone' => '021-12345678',
            'contact_address' => 'Jl. Pendidikan No. 123, Jakarta',
            'opening_time' => '08:00',
            'closing_time' => '17:00',
            'opening_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
        ]);
    }
}
