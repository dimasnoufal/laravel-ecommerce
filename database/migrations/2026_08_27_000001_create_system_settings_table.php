<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->default('general')->index();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string, integer, boolean, json
            $table->timestamps();
        });

        // Insert Default Store & System Settings
        $defaults = [
            // General Store Profile
            ['group' => 'general', 'key' => 'store_name', 'value' => 'Laravel E-Commerce', 'type' => 'string'],
            ['group' => 'general', 'key' => 'store_tagline', 'value' => 'Pusat Perbelanjaan Online Terpercaya & Berkualitas', 'type' => 'string'],
            ['group' => 'general', 'key' => 'store_email', 'value' => 'support@ecommerce.local', 'type' => 'string'],
            ['group' => 'general', 'key' => 'store_phone', 'value' => '081234567890', 'type' => 'string'],
            ['group' => 'general', 'key' => 'store_address', 'value' => 'Jl. Jenderal Sudirman Kav. 52-53, Jakarta Selatan, DKI Jakarta 12190', 'type' => 'string'],
            ['group' => 'general', 'key' => 'warehouse_city', 'value' => 'Jakarta Selatan', 'type' => 'string'],

            // Transaction & Tax Settings
            ['group' => 'transaction', 'key' => 'currency_code', 'value' => 'IDR', 'type' => 'string'],
            ['group' => 'transaction', 'key' => 'currency_symbol', 'value' => 'Rp', 'type' => 'string'],
            ['group' => 'transaction', 'key' => 'tax_percentage', 'value' => '0', 'type' => 'integer'],
            ['group' => 'transaction', 'key' => 'payment_expiry_hours', 'value' => '24', 'type' => 'integer'],
            ['group' => 'transaction', 'key' => 'min_order_amount', 'value' => '10000', 'type' => 'integer'],

            // System & Maintenance
            ['group' => 'system', 'key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean'],
            ['group' => 'system', 'key' => 'order_notification_email', 'value' => '1', 'type' => 'boolean'],
        ];

        $now = now();
        foreach ($defaults as &$item) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;
        }

        DB::table('system_settings')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
