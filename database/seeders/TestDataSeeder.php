<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Enums\Gender;
use App\Enums\MembershipStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Genre;
use App\Models\MemberProfile;
use App\Models\Publisher;
use App\Models\Shelf;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categories ────────────────────────────────────────────────
        $categories = [
            ['name' => 'Teknologi Informasi', 'slug' => 'teknologi-informasi'],
            ['name' => 'Sastra',              'slug' => 'sastra'],
            ['name' => 'Sains',               'slug' => 'sains'],
            ['name' => 'Sejarah',             'slug' => 'sejarah'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], [
                'name'   => $cat['name'],
                'status' => ActiveStatus::ACTIVE,
            ]);
        }

        // ── Genres ────────────────────────────────────────────────────
        $genres = ['Fiksi', 'Non-Fiksi', 'Tutorial', 'Referensi', 'Novel', 'Biografi'];
        foreach ($genres as $g) {
            Genre::firstOrCreate(['slug' => Str::slug($g)], [
                'name'   => $g,
                'status' => ActiveStatus::ACTIVE,
            ]);
        }

        // ── Publishers ────────────────────────────────────────────────
        $publishers = [
            ['name' => 'Gramedia',       'address' => 'Jakarta'],
            ['name' => 'Erlangga',       'address' => 'Jakarta'],
            ['name' => 'Andi Publisher', 'address' => 'Yogyakarta'],
        ];

        foreach ($publishers as $pub) {
            Publisher::firstOrCreate(['name' => $pub['name']], [
                'slug'    => \Illuminate\Support\Str::slug($pub['name']),
                'address' => $pub['address'],
                'status'  => ActiveStatus::ACTIVE,
            ]);
        }

        // ── Authors ───────────────────────────────────────────────────
        $authors = [
            ['name' => 'Budi Raharjo',    'email' => 'budi@example.com'],
            ['name' => 'Siti Aminah',     'email' => 'siti@example.com'],
            ['name' => 'Ahmad Fauzi',     'email' => 'ahmad@example.com'],
            ['name' => 'Dewi Lestari',    'email' => 'dewi@example.com'],
        ];

        foreach ($authors as $a) {
            Author::firstOrCreate(['slug' => Str::slug($a['name'])], [
                'name'   => $a['name'],
                'email'  => $a['email'],
                'status' => ActiveStatus::ACTIVE,
            ]);
        }

        // ── Shelves ───────────────────────────────────────────────────
        $shelves = [
            ['code' => 'RAK-A', 'name' => 'Rak A - Teknologi'],
            ['code' => 'RAK-B', 'name' => 'Rak B - Sastra'],
            ['code' => 'RAK-C', 'name' => 'Rak C - Sains'],
        ];

        foreach ($shelves as $s) {
            Shelf::firstOrCreate(['code' => $s['code']], [
                'name'   => $s['name'],
                'status' => ActiveStatus::ACTIVE,
            ]);
        }

        // ── Books ─────────────────────────────────────────────────────
        $categoryTI  = Category::where('slug', 'teknologi-informasi')->first();
        $categorySas = Category::where('slug', 'sastra')->first();
        $categorySai = Category::where('slug', 'sains')->first();
        $pubGramedia = Publisher::where('name', 'Gramedia')->first();
        $pubErlangga = Publisher::where('name', 'Erlangga')->first();
        $pubAndi     = Publisher::where('name', 'Andi Publisher')->first();
        $shelfA      = Shelf::where('code', 'RAK-A')->first();
        $shelfB      = Shelf::where('code', 'RAK-B')->first();
        $authorBudi  = Author::where('email', 'budi@example.com')->first();
        $authorSiti  = Author::where('email', 'siti@example.com')->first();
        $authorAhmad = Author::where('email', 'ahmad@example.com')->first();
        $authorDewi  = Author::where('email', 'dewi@example.com')->first();
        $genreTutorial   = Genre::where('name', 'Tutorial')->first();
        $genreReferensi  = Genre::where('name', 'Referensi')->first();
        $genreNonFiksi   = Genre::where('name', 'Non-Fiksi')->first();
        $genreNovel      = Genre::where('name', 'Novel')->first();

        $books = [
            [
                'title'           => 'Pemrograman Laravel untuk Pemula',
                'sku'             => 'BK-001',
                'isbn'            => '978-602-123-001',
                'category_id'     => $categoryTI->id,
                'publisher_id'    => $pubAndi->id,
                'shelf_id'        => $shelfA->id,
                'year'            => 2023,
                'initial_stock'   => 5,
                'authors'         => [$authorBudi->id],
                'genres'          => [$genreTutorial->id],
            ],
            [
                'title'           => 'Algoritma dan Struktur Data',
                'sku'             => 'BK-002',
                'isbn'            => '978-602-123-002',
                'category_id'     => $categoryTI->id,
                'publisher_id'    => $pubErlangga->id,
                'shelf_id'        => $shelfA->id,
                'year'            => 2022,
                'initial_stock'   => 3,
                'authors'         => [$authorAhmad->id],
                'genres'          => [$genreReferensi->id],
            ],
            [
                'title'           => 'Laskar Pelangi',
                'sku'             => 'BK-003',
                'isbn'            => '978-602-123-003',
                'category_id'     => $categorySas->id,
                'publisher_id'    => $pubGramedia->id,
                'shelf_id'        => $shelfB->id,
                'year'            => 2005,
                'initial_stock'   => 8,
                'authors'         => [$authorDewi->id],
                'genres'          => [$genreNovel->id],
            ],
            [
                'title'           => 'Fisika Dasar Jilid 1',
                'sku'             => 'BK-004',
                'isbn'            => '978-602-123-004',
                'category_id'     => $categorySai->id,
                'publisher_id'    => $pubErlangga->id,
                'shelf_id'        => $shelfA->id,
                'year'            => 2021,
                'initial_stock'   => 4,
                'authors'         => [$authorSiti->id],
                'genres'          => [$genreReferensi->id, $genreNonFiksi->id],
            ],
            [
                'title'           => 'Belajar Python dari Nol',
                'sku'             => 'BK-005',
                'isbn'            => '978-602-123-005',
                'category_id'     => $categoryTI->id,
                'publisher_id'    => $pubAndi->id,
                'shelf_id'        => $shelfA->id,
                'year'            => 2024,
                'initial_stock'   => 6,
                'authors'         => [$authorBudi->id, $authorAhmad->id],
                'genres'          => [$genreTutorial->id],
            ],
        ];

        foreach ($books as $b) {
            $authors = $b['authors'];
            $genres  = $b['genres'];
            unset($b['authors'], $b['genres']);

            $book = Book::firstOrCreate(['sku' => $b['sku']], array_merge($b, [
                'stock'           => $b['initial_stock'],
                'available_stock' => $b['initial_stock'],
                'status'          => ActiveStatus::ACTIVE,
                'slug'            => Str::slug($b['title']),
            ]));

            $book->authors()->syncWithoutDetaching($authors);
            $book->genres()->syncWithoutDetaching($genres);
        }

        // ── Members ───────────────────────────────────────────────────
        $members = [
            [
                'name'     => 'Andi Pratama',
                'email'    => 'andi@member.com',
                'gender'   => Gender::MALE,
                'phone'    => '081234567890',
                'address'  => 'Jl. Merdeka No. 1, Banda Aceh',
            ],
            [
                'name'     => 'Sari Dewi',
                'email'    => 'sari@member.com',
                'gender'   => Gender::FEMALE,
                'phone'    => '082345678901',
                'address'  => 'Jl. Sudirman No. 5, Banda Aceh',
            ],
            [
                'name'     => 'Rizky Fadhillah',
                'email'    => 'rizky@member.com',
                'gender'   => Gender::MALE,
                'phone'    => '083456789012',
                'address'  => 'Jl. Diponegoro No. 10, Banda Aceh',
            ],
        ];

        foreach ($members as $m) {
            $user = User::firstOrCreate(['email' => $m['email']], [
                'name'     => $m['name'],
                'password' => Hash::make('password'),
            ]);

            MemberProfile::firstOrCreate(['user_id' => $user->id], [
                'member_code'       => 'M-' . now()->format('dmY') . '-' . rand(10000000, 99999999),
                'identity_number'   => '000000000000' . rand(1000, 9999),
                'full_name'         => $m['name'],
                'gender'            => $m['gender'],
                'phone_number'      => $m['phone'],
                'address'           => $m['address'],
                'registration_date' => now(),
                'membership_status' => MembershipStatus::ACTIVE,
            ]);
        }

        $this->command->info('✅ Test data seeded: 4 kategori, 6 genre, 3 penerbit, 4 penulis, 5 buku, 3 anggota.');
    }
}
