<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Apple',
            'Samsung',
            'Nike',
            'Adidas',
            'Sony',
            'Asus',
            'Logitech',
            'Xiaomi',
            'Uniqlo',
            'Zara'
        ];

        foreach ($brands as $name) {
            Brand::firstOrCreate([
                'name' => $name
            ], [
                'slug' => Str::slug($name)
            ]);
        }
    }
}
