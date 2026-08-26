<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\AuditLog;
use App\Enums\StockMovementType;
use App\Enums\AuditLogAction;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variants = ProductVariant::all();
        if ($variants->isEmpty()) return;

        foreach ($variants as $variant) {
            // Initial Inflow
            $initialStock = $variant->stock > 0 ? $variant->stock : 25;
            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => StockMovementType::IN->value,
                'quantity' => $initialStock,
                'note' => 'Stok awal inisialisasi batch supplier',
                'created_at' => now()->subDays(rand(7, 14)),
            ]);

            // Sample Adjustment / Sales Outflow
            if ($initialStock > 10) {
                $outQty = rand(2, 5);
                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => StockMovementType::OUT->value,
                    'quantity' => -$outQty,
                    'note' => 'Pengurangan stok untuk pesanan customer checkout',
                    'created_at' => now()->subDays(rand(1, 5)),
                ]);
            }

            // Sample Audit Log
            AuditLog::create([
                'user_id' => 1,
                'action' => AuditLogAction::CREATE->value,
                'auditable_type' => ProductVariant::class,
                'auditable_id' => $variant->id,
                'description' => 'Inisialisasi katalog varian SKU ' . $variant->sku,
                'metadata' => [
                    'sku' => $variant->sku,
                    'stock' => $variant->stock,
                    'price' => $variant->price,
                ],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/128.0.0.0',
                'created_at' => now()->subDays(rand(1, 10)),
            ]);
        }
    }
}
