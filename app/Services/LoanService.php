<?php

namespace App\Services;

use App\Actions\Loan\CreateLoanAction;
use App\Actions\Loan\ProcessReturnAction;
use App\Models\Loan;
use App\Models\LoanRule;
use App\Models\MemberProfile;
use App\Models\User;

class LoanService
{
    public function __construct(
        private CreateLoanAction $createLoanAction,
        private ProcessReturnAction $processReturnAction
    ) {}

    /**
     * Create new loan transaction
     */
    public function createLoan(
        MemberProfile $member,
        array $bookIds,
        ?int $loanRuleId = null,
        ?User $creator = null,
        ?string $notes = null,
        $loanDate = null
    ): Loan {
        $loanRule = $loanRuleId
            ? LoanRule::findOrFail($loanRuleId)
            : LoanRule::active()->first();

        if (!$loanRule) {
            throw new \Exception('Tidak ada aturan peminjaman yang aktif.');
        }

        $creator = $creator ?? auth()->user();

        return $this->createLoanAction->execute($member, $bookIds, $loanRule, $creator, $notes, $loanDate);
    }

    /**
     * Process book return
     */
    public function processReturn(
        Loan $loan,
        array $returnData,
        ?User $returner = null
    ): Loan {
        $returner = $returner ?? auth()->user();

        return $this->processReturnAction->execute($loan, $returnData, $returner);
    }

    /**
     * Get member active loans
     */
    public function getMemberActiveLoans(MemberProfile $member)
    {
        return Loan::where('member_id', $member->id)
            ->active()
            ->with(['details.book', 'loanRule'])
            ->orderBy('loan_date', 'desc')
            ->get();
    }

    public function getMemberLoanHistory(MemberProfile $member)
    {
        return Loan::where('member_id', $member->id)
            ->with(['details.book', 'loanRule'])
            ->orderBy('loan_date', 'desc')
            ->paginate(10);
    }

    /**
     * Check if member can borrow more books
     */
    public function canMemberBorrow(MemberProfile $member, int $additionalBooks = 1): bool
    {
        if (!$member->canBorrow()) {
            return false;
        }

        $loanRule = LoanRule::active()->first();
        if (!$loanRule) {
            return false;
        }

        $activeLoanCount = $member->getActiveLoanCount();
        return ($activeLoanCount + $additionalBooks) <= $loanRule->max_active_loans;
    }
}
