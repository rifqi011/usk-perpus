<?php

namespace App\Actions\Reservation;

use App\Enums\ReservationStatus;
use App\Models\Book;
use App\Models\MemberProfile;
use App\Models\Reservation;

class CreateReservationAction
{
    public function execute(MemberProfile $member, Book $book, ?string $notes = null): Reservation
    {
        // Validasi member bisa reservasi
        if (!$member->canBorrow()) {
            throw new \Exception('Anggota tidak dapat melakukan reservasi. Status: ' . $member->membership_status->label());
        }

        // Cek apakah buku tersedia
        if ($book->isAvailable()) {
            throw new \Exception('Buku masih tersedia. Silakan pinjam langsung.');
        }

        // Cek apakah member sudah punya reservasi aktif untuk buku ini
        $existingReservation = Reservation::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereIn('status', [ReservationStatus::WAITING, ReservationStatus::READY])
            ->exists();

        if ($existingReservation) {
            throw new \Exception('Anda sudah memiliki reservasi aktif untuk buku ini.');
        }

        // Create reservation
        return Reservation::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'reservation_date' => now(),
            'expire_at' => now()->addDays(3), // Reservasi berlaku 3 hari
            'status' => ReservationStatus::WAITING,
            'notes' => $notes,
        ]);
    }
}
