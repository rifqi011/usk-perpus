<?php

namespace App\Services;

use App\Enums\FineStatus;
use App\Models\Fine;
use App\Models\MemberProfile;

class FineService
{
    /**
     * Get member unpaid fines
     */
    public function getMemberUnpaidFines(MemberProfile $member)
    {
        return Fine::where('member_id', $member->id)
            ->unpaid()
            ->with(['loanDetail.bookCopy.book', 'loanDetail.loan'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get member fine history
     */
    public function getMemberFineHistory(MemberProfile $member)
    {
        return Fine::where('member_id', $member->id)
            ->with(['loanDetail.bookCopy.book', 'loanDetail.loan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * Mark fine as paid
     */
    public function markAsPaid(Fine $fine): Fine
    {
        $fine->markAsPaid();
        return $fine;
    }

    /**
     * Waive fine
     */
    public function waiveFine(Fine $fine): Fine
    {
        $fine->waive();
        return $fine;
    }

    /**
     * Get total unpaid fines for member
     */
    public function getTotalUnpaidFines(MemberProfile $member): float
    {
        return Fine::where('member_id', $member->id)
            ->where('status', FineStatus::UNPAID)
            ->sum('amount');
    }

    /**
     * Get overdue loans with unpaid fines
     */
    public function getOverdueLoansWithFines()
    {
        return Fine::unpaid()
            ->with(['member.user', 'loanDetail.loan', 'loanDetail.bookCopy.book'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
}
