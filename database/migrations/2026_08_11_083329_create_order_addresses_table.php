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
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('type', array_column(\App\Enums\OrderAddressType::cases(), 'value'))->default(\App\Enums\OrderAddressType::SHIPPING->value);
            $table->string('recipient_name');
            $table->string('phone');
            $table->text('address_line');
            $table->string('country_name');
            $table->string('province_name');
            $table->string('regency_name');
            $table->string('district_name');
            $table->string('village_name');
            $table->string('postal_code')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['order_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
