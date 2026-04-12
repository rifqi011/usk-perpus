<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_detail_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('member_profiles')->cascadeOnDelete();
            $table->enum('fine_type', ['late_return', 'minor_damage', 'major_damage', 'lost_book', 'other']);
            $table->enum('calculation_type', ['per_day', 'fixed', 'book_price', 'manual']);
            $table->integer('qty')->default(1);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['unpaid', 'paid', 'waived'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('loan_detail_id');
            $table->index('member_id');
            $table->index('status');
            $table->index('fine_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
