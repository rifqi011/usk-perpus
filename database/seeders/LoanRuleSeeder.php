<?php

namespace Database\Seeders;

use App\Models\LoanRule;
use Illuminate\Database\Seeder;

class LoanRuleSeeder extends Seeder
{
    public function run(): void
    {
        // Aturan Default
        LoanRule::updateOrCreate(
            ['code' => 'DEFAULT'],
            [
                'name' => 'Aturan Peminjaman Umum',
                'max_active_loans' => 3,
                'max_loan_days' => 7,
                'grace_days' => 0,
                'fine_per_day' => 1000,
                'can_renew' => true,
                'max_renew_count' => 1,
                'damage_fine_minor' => 10000,
                'damage_fine_major' => 50000,
                'lost_book_fine_type' => 'book_price',
                'lost_book_fine_amount' => 0,
                'is_active' => true,
            ]
        );

        // Aturan untuk Mahasiswa
        LoanRule::updateOrCreate(
            ['code' => 'STUDENT'],
            [
                'name' => 'Aturan Peminjaman Mahasiswa',
                'max_active_loans' => 5,
                'max_loan_days' => 14,
                'grace_days' => 1,
                'fine_per_day' => 500,
                'can_renew' => true,
                'max_renew_count' => 2,
                'damage_fine_minor' => 10000,
                'damage_fine_major' => 50000,
                'lost_book_fine_type' => 'book_price',
                'lost_book_fine_amount' => 0,
                'is_active' => true,
            ]
        );

        // Aturan untuk Dosen
        LoanRule::updateOrCreate(
            ['code' => 'LECTURER'],
            [
                'name' => 'Aturan Peminjaman Dosen',
                'max_active_loans' => 10,
                'max_loan_days' => 30,
                'grace_days' => 3,
                'fine_per_day' => 0,
                'can_renew' => true,
                'max_renew_count' => 3,
                'damage_fine_minor' => 10000,
                'damage_fine_major' => 50000,
                'lost_book_fine_type' => 'book_price',
                'lost_book_fine_amount' => 0,
                'is_active' => true,
            ]
        );
    }
}
