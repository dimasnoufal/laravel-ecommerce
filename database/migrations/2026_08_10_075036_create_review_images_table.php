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
        Schema::create('review_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->string('image_path');
            $table->integer('sort_order');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['review_id', 'sort_order']);
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE review_images ADD CONSTRAINT check_review_images_sort_order CHECK (sort_order > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_images');
    }
};
