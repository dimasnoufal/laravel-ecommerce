<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $coordinates = [
            ['group' => 'general', 'key' => 'warehouse_latitude', 'value' => '-6.2297465', 'type' => 'string'],
            ['group' => 'general', 'key' => 'warehouse_longitude', 'value' => '106.8164494', 'type' => 'string'],
            ['group' => 'general', 'key' => 'map_zoom_level', 'value' => '15', 'type' => 'integer'],
        ];

        $now = now();
        foreach ($coordinates as $item) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;

            DB::table('system_settings')->updateOrInsert(
                ['key' => $item['key']],
                $item
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', ['warehouse_latitude', 'warehouse_longitude', 'map_zoom_level'])->delete();
    }
};
