<?php

namespace App\Actions\Loan;

use App\Enums\FineStatus;
use App\Models\Fine;
use App\Models\LoanDetail;
use App\Models\LoanRule;

class CalculateFineAction
{
    public function execute(LoanDetail $loanDetail, LoanRule $loanRule): void
    {
        if ($loanDetail->fine_generated) {
            return;
        }

        $totalFine = 0;

        // Denda keterlambatan
        if ($loanDetail->late_days > 0) {
            $effectiveLateDays = max(0, $loanDetail->late_days - ($loanRule->grace_days ?? 0));

            if ($effectiveLateDays > 0) {
                $lateFineAmount = $effectiveLateDays * $loanRule->fine_per_day;

                Fine::create([
                    'loan_detail_id' => $loanDetail->id,
                    'member_id'      => $loanDetail->loan->member_id,
                    'fine_type'      => 'late_return',
                    'calculation_type' => 'per_day',
                    'qty'            => $effectiveLateDays,
                    'rate'           => $loanRule->fine_per_day,
                    'amount'         => $lateFineAmount,
                    'status'         => FineStatus::UNPAID,
                    'notes'          => "Denda keterlambatan {$effectiveLateDays} hari",
                ]);

                $totalFine += $lateFineAmount;
            }
        }

        $loanDetail->update([
            'fine_amount'    => $totalFine,
            'fine_generated' => true,
        ]);
    }
}
