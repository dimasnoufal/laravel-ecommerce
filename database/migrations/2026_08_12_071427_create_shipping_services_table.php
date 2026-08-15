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
        Schema::create('shipping_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('shipping_carriers')->restrictOnDelete();
            $table->string('code');
            $table->string('name');
            $table->integer('estimated_min_days');
            $table->integer('estimated_max_days');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['carrier_id', 'code']);
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE shipping_services ADD CONSTRAINT check_shipping_services_min_days CHECK (estimated_min_days >= 0)');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE shipping_services ADD CONSTRAINT check_shipping_services_max_days CHECK (estimated_max_days >= estimated_min_days)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_services');
    }
};
