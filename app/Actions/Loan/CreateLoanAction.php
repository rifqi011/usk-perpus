<?php

namespace App\Actions\Loan;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\LoanRule;
use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateLoanAction
{
    public function execute(
        MemberProfile $member,
        array $bookIds,
        LoanRule $loanRule,
        User $creator,
        ?string $notes = null,
        $loanDate = null
    ): Loan {
        // Validasi member bisa pinjam
        if (!$member->canBorrow()) {
            throw new \Exception('Anggota tidak dapat meminjam buku. Status: ' . $member->membership_status->label());
        }

        // Cek batas maksimal pinjaman aktif
        $activeLoanCount = $member->getActiveLoanCount();
        if ($activeLoanCount >= $loanRule->max_active_loans) {
            throw new \Exception("Batas maksimal peminjaman aktif ({$loanRule->max_active_loans}) telah tercapai.");
        }

        $totalBooks = count($bookIds) + $activeLoanCount;
        if ($totalBooks > $loanRule->max_active_loans) {
            throw new \Exception("Total peminjaman akan melebihi batas maksimal ({$loanRule->max_active_loans}).");
        }

        // Cek ketersediaan stok semua buku
        $books = Book::whereIn('id', $bookIds)->get();

        if ($books->count() !== count($bookIds)) {
            throw new \Exception('Beberapa buku tidak ditemukan.');
        }

        foreach ($books as $book) {
            if ($book->available_stock <= 0) {
                throw new \Exception("Buku \"{$book->title}\" tidak tersedia (stok habis).");
            }
        }

        return DB::transaction(function () use ($member, $books, $loanRule, $creator, $notes, $loanDate) {
            $loanDate = $loanDate ? \Carbon\Carbon::parse($loanDate) : now();
            $dueDate = $loanRule->calculateDueDate($loanDate->toDateTime());

            // Buat header peminjaman
            $loan = Loan::create([
                'loan_code'    => $this->generateLoanCode(),
                'member_id'    => $member->id,
                'loan_rule_id' => $loanRule->id,
                'loan_date'    => $loanDate,
                'due_date'     => $dueDate,
                'status'       => LoanStatus::BORROWED,
                'notes'        => $notes,
                'created_by'   => $creator->id,
                'approved_by'  => $creator->id,
            ]);

            // Buat detail & kurangi stok
            foreach ($books as $book) {
                LoanDetail::create([
                    'loan_id'  => $loan->id,
                    'book_id'  => $book->id,
                    'due_date' => $dueDate,
                    'status'   => LoanStatus::BORROWED,
                ]);

                $book->decrement('available_stock');
            }

            return $loan->load('details.book');
        });
    }

    private function generateLoanCode(): string
    {
        $year  = now()->year;
        $month = now()->format('m');

        $lastLoan = Loan::whereYear('loan_date', $year)
            ->whereMonth('loan_date', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastLoan ? intval(substr($lastLoan->loan_code, -4)) + 1 : 1;

        return sprintf('LN%s%s%04d', $year, $month, $sequence);
    }
}
