<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code')->unique();
            $table->foreignId('member_id')->constrained('member_profiles')->cascadeOnDelete();
            $table->foreignId('loan_rule_id')->nullable()->constrained()->nullOnDelete();
            $table->date('loan_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'partially_returned', 'overdue', 'lost'])->default('borrowed');
            $table->decimal('total_fine', 10, 2)->default(0);
            $table->integer('total_late_days')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('returned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('return_processed_at')->nullable();
            $table->text('return_notes')->nullable();
            $table->timestamps();

            $table->index('loan_code');
            $table->index('status');
            $table->index('member_id');
            $table->index('loan_date');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
