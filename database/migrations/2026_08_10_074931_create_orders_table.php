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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('order_number')->unique();
            $table->enum('status', array_column(\App\Enums\OrderStatus::cases(), 'value'))->default(\App\Enums\OrderStatus::PENDING->value);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders ADD CONSTRAINT check_orders_subtotal CHECK (subtotal >= 0)');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders ADD CONSTRAINT check_orders_shipping_cost CHECK (shipping_cost >= 0)');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders ADD CONSTRAINT check_orders_discount_amount CHECK (discount_amount >= 0)');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders ADD CONSTRAINT check_orders_total_amount CHECK (total_amount >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
