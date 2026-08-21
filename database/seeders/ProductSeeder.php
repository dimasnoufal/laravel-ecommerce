<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apple = Brand::where('name', 'Apple')->first();
        $nike = Brand::where('name', 'Nike')->first();
        $sony = Brand::where('name', 'Sony')->first();

        $smartphone = Category::where('name', 'Smartphone & Tablet')->first();
        $audio = Category::where('name', 'Audio & Headphone')->first();
        $sepatu = Category::where('name', 'Sepatu & Sneakers')->first();

        // 1. iPhone 15 Pro Max
        $iphone = Product::updateOrCreate(
            ['name' => 'iPhone 15 Pro Max 256GB Natural Titanium'],
            [
                'slug' => Str::slug('iPhone 15 Pro Max 256GB Natural Titanium'),
                'category_id' => $smartphone?->id,
                'brand_id' => $apple?->id,
                'description' => 'Flagship smartphone dari Apple dengan chip A17 Pro bertenaga monster dan bodi titanium aero-grade yang kokoh nan ringan.',
                'is_active' => true,
            ]
        );

        $cap256 = AttributeValue::where('value', '256GB')->first();
        $cap512 = AttributeValue::where('value', '512GB')->first();
        $cap1tb = AttributeValue::where('value', '1TB')->first();

        $v1 = ProductVariant::updateOrCreate(
            ['product_id' => $iphone->id, 'sku' => 'IPH15PM-NAT-256GB'],
            ['price' => 21999000, 'stock' => 25, 'is_active' => true]
        );
        if ($cap256) $v1->attributeValues()->syncWithoutDetaching([$cap256->id]);

        $v2 = ProductVariant::updateOrCreate(
            ['product_id' => $iphone->id, 'sku' => 'IPH15PM-NAT-512GB'],
            ['price' => 25999000, 'stock' => 15, 'is_active' => true]
        );
        if ($cap512) $v2->attributeValues()->syncWithoutDetaching([$cap512->id]);

        $v3 = ProductVariant::updateOrCreate(
            ['product_id' => $iphone->id, 'sku' => 'IPH15PM-NAT-1TB'],
            ['price' => 29999000, 'stock' => 8, 'is_active' => true]
        );
        if ($cap1tb) $v3->attributeValues()->syncWithoutDetaching([$cap1tb->id]);

        // 2. Sony WH-1000XM5
        $sonyHeadphone = Product::updateOrCreate(
            ['name' => 'Sony WH-1000XM5 Wireless Noise Canceling Headphones'],
            [
                'slug' => Str::slug('Sony WH-1000XM5 Wireless Noise Canceling Headphones'),
                'category_id' => $audio?->id,
                'brand_id' => $sony?->id,
                'description' => 'Headphone premium dengan active noise cancelling terbaik di kelasnya, mikrofon ganda, dan baterai tahan 30 jam.',
                'is_active' => true,
            ]
        );

        $colorHitam = AttributeValue::where('value', 'Hitam')->first();
        $colorPutih = AttributeValue::where('value', 'Putih')->first();
        $colorNavy = AttributeValue::where('value', 'Biru Navy')->first();

        $sv1 = ProductVariant::updateOrCreate(
            ['product_id' => $sonyHeadphone->id, 'sku' => 'SONY-XM5-BLK'],
            ['price' => 5499000, 'stock' => 20, 'is_active' => true]
        );
        if ($colorHitam) $sv1->attributeValues()->syncWithoutDetaching([$colorHitam->id]);

        $sv2 = ProductVariant::updateOrCreate(
            ['product_id' => $sonyHeadphone->id, 'sku' => 'SONY-XM5-SLV'],
            ['price' => 5499000, 'stock' => 15, 'is_active' => true]
        );
        if ($colorPutih) $sv2->attributeValues()->syncWithoutDetaching([$colorPutih->id]);

        $sv3 = ProductVariant::updateOrCreate(
            ['product_id' => $sonyHeadphone->id, 'sku' => 'SONY-XM5-NVY'],
            ['price' => 5799000, 'stock' => 10, 'is_active' => true]
        );
        if ($colorNavy) $sv3->attributeValues()->syncWithoutDetaching([$colorNavy->id]);

        // 3. Nike Air Jordan 1
        $nikeJordan = Product::updateOrCreate(
            ['name' => 'Nike Air Jordan 1 Retro High OG'],
            [
                'slug' => Str::slug('Nike Air Jordan 1 Retro High OG'),
                'category_id' => $sepatu?->id,
                'brand_id' => $nike?->id,
                'description' => 'Sneakers legendaris edisi retro dengan siluet ikonik Chicago colorway dan bantalan Air-Sole.',
                'is_active' => true,
            ]
        );

        $sizeM = AttributeValue::where('value', 'M')->first();
        $sizeL = AttributeValue::where('value', 'L')->first();
        $sizeXL = AttributeValue::where('value', 'XL')->first();

        $nv1 = ProductVariant::updateOrCreate(
            ['product_id' => $nikeJordan->id, 'sku' => 'NIKE-AJ1-M'],
            ['price' => 2899000, 'stock' => 14, 'is_active' => true]
        );
        if ($sizeM) $nv1->attributeValues()->syncWithoutDetaching([$sizeM->id]);

        $nv2 = ProductVariant::updateOrCreate(
            ['product_id' => $nikeJordan->id, 'sku' => 'NIKE-AJ1-L'],
            ['price' => 2899000, 'stock' => 22, 'is_active' => true]
        );
        if ($sizeL) $nv2->attributeValues()->syncWithoutDetaching([$sizeL->id]);

        $nv3 = ProductVariant::updateOrCreate(
            ['product_id' => $nikeJordan->id, 'sku' => 'NIKE-AJ1-XL'],
            ['price' => 2999000, 'stock' => 12, 'is_active' => true]
        );
        if ($sizeXL) $nv3->attributeValues()->syncWithoutDetaching([$sizeXL->id]);
    }
}

