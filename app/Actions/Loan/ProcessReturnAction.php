<?php

namespace App\Actions\Loan;

use App\Enums\LoanStatus;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProcessReturnAction
{
    public function __construct(
        private CalculateFineAction $calculateFineAction
    ) {}

    public function execute(
        Loan $loan,
        array $returnData, // ['loan_detail_id' => ['notes' => '...']]
        User $returner
    ): Loan {
        if (!$loan->isActive()) {
            throw new \Exception('Peminjaman ini sudah tidak aktif.');
        }

        return DB::transaction(function () use ($loan, $returnData, $returner) {
            foreach ($returnData as $detailId => $data) {
                $detail = LoanDetail::findOrFail($detailId);

                if ($detail->loan_id !== $loan->id) {
                    throw new \Exception('Detail peminjaman tidak sesuai.');
                }

                if ($detail->isReturned()) {
                    continue;
                }

                // Hitung keterlambatan
                $lateDays = $detail->due_date->isPast()
                    ? (int) now()->startOfDay()->diffInDays($detail->due_date->startOfDay())
                    : 0;

                $detail->update([
                    'returned_at'  => now(),
                    'late_days'    => $lateDays,
                    'status'       => LoanStatus::RETURNED,
                    'returned_by'  => $returner->id,
                    'return_notes' => $data['notes'] ?? null,
                ]);

                // Hitung denda
                $this->calculateFineAction->execute($detail, $loan->loanRule);

                // Kembalikan stok buku — pastikan tidak melebihi total stok
                $book = $detail->book;
                if ($book->available_stock < $book->stock) {
                    $book->increment('available_stock');
                }
            }

            // Cek apakah semua buku sudah dikembalikan
            $allReturned = $loan->details()->where('status', '!=', LoanStatus::RETURNED)->count() === 0;

            if ($allReturned) {
                $totalFine    = $loan->details()->sum('fine_amount');
                $totalLateDays = $loan->details()->max('late_days');

                $loan->update([
                    'status'              => LoanStatus::RETURNED,
                    'return_date'         => now(),
                    'return_processed_at' => now(),
                    'returned_by'         => $returner->id,
                    'total_fine'          => $totalFine,
                    'total_late_days'     => $totalLateDays,
                ]);
            } else {
                $loan->update(['status' => LoanStatus::PARTIALLY_RETURNED]);
            }

            return $loan->fresh(['details.book', 'details.fines']);
        });
    }
}
