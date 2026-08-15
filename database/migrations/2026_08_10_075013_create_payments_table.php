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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete();
            $table->string('provider', 50);
            $table->string('provider_reference', 150);
            $table->decimal('amount', 15, 2);
            $table->enum('status', array_column(\App\Enums\PaymentStatus::cases(), 'value'))->default(\App\Enums\PaymentStatus::PENDING->value);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            
            $table->unique(['provider', 'provider_reference']);
            $table->index('order_id');
            $table->index(['status', 'created_at']);
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE payments ADD CONSTRAINT check_payments_amount CHECK (amount >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
