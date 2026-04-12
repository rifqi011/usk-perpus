<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('max_active_loans')->default(3);
            $table->integer('max_loan_days')->default(7);
            $table->decimal('fine_per_day', 10, 2)->default(1000);
            $table->integer('grace_days')->default(0);
            $table->boolean('can_renew')->default(true);
            $table->integer('max_renew_count')->default(1);
            $table->decimal('damage_fine_minor', 10, 2)->default(10000);
            $table->decimal('damage_fine_major', 10, 2)->default(50000);
            $table->enum('lost_book_fine_type', ['fixed', 'book_price'])->default('book_price');
            $table->decimal('lost_book_fine_amount', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_rules');
    }
};
