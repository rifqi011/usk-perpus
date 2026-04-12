<?php

if (!function_exists('format_currency')) {
    /**
     * Format number as Indonesian Rupiah
     */
    function format_currency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date to Indonesian format
     */
    function format_date($date, string $format = 'd F Y'): string
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->translatedFormat($format);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime to Indonesian format
     */
    function format_datetime($datetime): string
    {
        return format_date($datetime, 'd F Y H:i');
    }
}

if (!function_exists('calculate_late_days')) {
    /**
     * Calculate late days from due date
     */
    function calculate_late_days($dueDate, $returnDate = null): int
    {
        $dueDate = \Carbon\Carbon::parse($dueDate);
        $returnDate = $returnDate ? \Carbon\Carbon::parse($returnDate) : now();

        if ($returnDate->lte($dueDate)) {
            return 0;
        }

        return $dueDate->diffInDays($returnDate);
    }
}

if (!function_exists('generate_barcode')) {
    /**
     * Generate unique barcode for book copy
     */
    function generate_barcode(): string
    {
        return 'BC' . now()->format('Ymd') . strtoupper(substr(uniqid(), -6));
    }
}

if (!function_exists('generate_inventory_code')) {
    /**
     * Generate unique inventory code for book copy
     */
    function generate_inventory_code(int $bookId): string
    {
        $year = now()->year;
        $sequence = \App\Models\BookCopy::where('book_id', $bookId)->count() + 1;
        return sprintf('INV%s%05d%03d', $year, $bookId, $sequence);
    }
}

if (!function_exists('get_book_condition_badge')) {
    /**
     * Get HTML badge for book condition
     */
    function get_book_condition_badge(string $condition): string
    {
        $colors = [
            'new' => 'bg-green-100 text-green-800',
            'good' => 'bg-green-100 text-green-800',
            'fair' => 'bg-yellow-100 text-yellow-800',
            'minor_damage' => 'bg-orange-100 text-orange-800',
            'major_damage' => 'bg-red-100 text-red-800',
            'lost' => 'bg-gray-100 text-gray-800',
        ];

        $labels = [
            'new' => 'Baru',
            'good' => 'Baik',
            'fair' => 'Cukup',
            'minor_damage' => 'Rusak Ringan',
            'major_damage' => 'Rusak Berat',
            'lost' => 'Hilang',
        ];

        $color = $colors[$condition] ?? 'bg-gray-100 text-gray-800';
        $label = $labels[$condition] ?? ucfirst($condition);

        return "<span class=\"px-2 py-1 text-xs font-semibold rounded-full {$color}\">{$label}</span>";
    }
}

if (!function_exists('get_loan_status_badge')) {
    /**
     * Get HTML badge for loan status
     */
    function get_loan_status_badge(string $status): string
    {
        $colors = [
            'borrowed' => 'bg-blue-100 text-blue-800',
            'returned' => 'bg-green-100 text-green-800',
            'partially_returned' => 'bg-yellow-100 text-yellow-800',
            'overdue' => 'bg-red-100 text-red-800',
            'lost' => 'bg-gray-100 text-gray-800',
        ];

        $labels = [
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'partially_returned' => 'Sebagian Dikembalikan',
            'overdue' => 'Terlambat',
            'lost' => 'Hilang',
        ];

        $color = $colors[$status] ?? 'bg-gray-100 text-gray-800';
        $label = $labels[$status] ?? ucfirst($status);

        return "<span class=\"px-2 py-1 text-xs font-semibold rounded-full {$color}\">{$label}</span>";
    }
}

if (!function_exists('is_overdue')) {
    /**
     * Check if loan is overdue
     */
    function is_overdue($dueDate): bool
    {
        return \Carbon\Carbon::parse($dueDate)->isPast();
    }
}

if (!function_exists('get_active_loan_rule')) {
    /**
     * Get active loan rule
     */
    function get_active_loan_rule(): ?\App\Models\LoanRule
    {
        return \App\Models\LoanRule::active()->first();
    }
}
