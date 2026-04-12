<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('publisher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shelf_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('isbn')->unique()->nullable();
            $table->string('sku')->unique()->nullable();
            $table->year('year')->nullable();
            $table->string('edition')->nullable();
            $table->string('language')->default('Indonesia');
            $table->integer('page_count')->nullable();
            $table->text('description')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('cover_image')->nullable();
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('replacement_price', 10, 2)->default(0);
            $table->integer('initial_stock')->default(0);
            $table->integer('stock')->default(0);
            $table->integer('available_stock')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('isbn');
            $table->index('sku');
            $table->index('status');
            $table->index('category_id');
            $table->index('publisher_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
