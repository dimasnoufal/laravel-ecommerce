<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->restrictOnDelete();
            $table->smallInteger('rating');
            $table->string('title', 200)->nullable();
            $table->text('comment')->nullable();
            $table->enum('status', array_column(\App\Enums\ReviewStatus::cases(), 'value'))->default(\App\Enums\ReviewStatus::PENDING->value);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique('order_item_id');
            $table->index('user_id');
            $table->index(['product_id', 'status', 'created_at']);
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE reviews ADD CONSTRAINT check_reviews_rating CHECK (rating >= 1 AND rating <= 5)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
