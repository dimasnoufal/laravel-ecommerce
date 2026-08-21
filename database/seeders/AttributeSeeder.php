<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributesData = [
            'Warna' => ['Hitam', 'Putih', 'Biru Navy', 'Merah', 'Abu-Abu', 'Hijau Army'],
            'Ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            'Kapasitas' => ['64GB', '128GB', '256GB', '512GB', '1TB'],
            'Bahan' => ['Katun Combed 30s', 'Polyester', 'Kulit Sintetis', 'Denim', 'Titanium'],
        ];

        foreach ($attributesData as $attrName => $values) {
            $attribute = Attribute::firstOrCreate([
                'name' => $attrName,
            ], [
                'slug' => Str::slug($attrName),
            ]);

            foreach ($values as $val) {
                AttributeValue::firstOrCreate([
                    'attribute_id' => $attribute->id,
                    'value' => $val,
                ], [
                    'slug' => Str::slug($val),
                ]);
            }
        }
    }
}
