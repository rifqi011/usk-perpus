<?php

namespace App\Services;

use App\Actions\Reservation\CreateReservationAction;
use App\Enums\ReservationStatus;
use App\Models\Book;
use App\Models\MemberProfile;
use App\Models\Reservation;

class ReservationService
{
    public function __construct(
        private CreateReservationAction $createReservationAction
    ) {}

    /**
     * Create new reservation
     */
    public function createReservation(
        MemberProfile $member,
        Book $book,
        ?string $notes = null
    ): Reservation {
        return $this->createReservationAction->execute($member, $book, $notes);
    }

    /**
     * Get member active reservations
     */
    public function getMemberActiveReservations(MemberProfile $member)
    {
        return Reservation::where('member_id', $member->id)
            ->active()
            ->with('book')
            ->orderBy('reservation_date', 'desc')
            ->get();
    }

    /**
     * Get member reservation history
     */
    public function getMemberReservationHistory(MemberProfile $member)
    {
        return Reservation::where('member_id', $member->id)
            ->with('book')
            ->orderBy('reservation_date', 'desc')
            ->paginate(10);
    }

    /**
     * Cancel reservation
     */
    public function cancelReservation(Reservation $reservation): Reservation
    {
        if (!$reservation->isActive()) {
            throw new \Exception('Reservasi tidak dapat dibatalkan.');
        }

        $reservation->update(['status' => ReservationStatus::CANCELLED]);
        return $reservation;
    }

    /**
     * Mark reservation as ready
     */
    public function markAsReady(Reservation $reservation): Reservation
    {
        if ($reservation->status !== ReservationStatus::WAITING) {
            throw new \Exception('Reservasi tidak dalam status menunggu.');
        }

        $reservation->update([
            'status' => ReservationStatus::READY,
            'expire_at' => now()->addDays(2), // Ready selama 2 hari
        ]);

        return $reservation;
    }

    /**
     * Mark reservation as fulfilled
     */
    public function markAsFulfilled(Reservation $reservation): Reservation
    {
        $reservation->update(['status' => ReservationStatus::FULFILLED]);
        return $reservation;
    }

    /**
     * Expire old reservations
     */
    public function expireOldReservations(): int
    {
        return Reservation::where('status', ReservationStatus::READY)
            ->where('expire_at', '<', now())
            ->update(['status' => ReservationStatus::EXPIRED]);
    }
}
