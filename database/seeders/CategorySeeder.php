<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::firstOrCreate(['name' => 'Elektronik'], ['slug' => 'elektronik']);
        Category::firstOrCreate(['name' => 'Smartphone & Tablet'], ['slug' => 'smartphone-tablet', 'parent_id' => $electronics->id]);
        Category::firstOrCreate(['name' => 'Laptop & Komputer'], ['slug' => 'laptop-komputer', 'parent_id' => $electronics->id]);
        Category::firstOrCreate(['name' => 'Audio & Headphone'], ['slug' => 'audio-headphone', 'parent_id' => $electronics->id]);

        $fashion = Category::firstOrCreate(['name' => 'Fashion & Pakaian'], ['slug' => 'fashion-pakaian']);
        Category::firstOrCreate(['name' => 'Pakaian Pria'], ['slug' => 'pakaian-pria', 'parent_id' => $fashion->id]);
        Category::firstOrCreate(['name' => 'Pakaian Wanita'], ['slug' => 'pakaian-wanita', 'parent_id' => $fashion->id]);
        Category::firstOrCreate(['name' => 'Sepatu & Sneakers'], ['slug' => 'sepatu-sneakers', 'parent_id' => $fashion->id]);

        $home = Category::firstOrCreate(['name' => 'Rumah Tangga & Dapur'], ['slug' => 'rumah-tangga-dapur']);
        Category::firstOrCreate(['name' => 'Peralatan Masak'], ['slug' => 'peralatan-masak', 'parent_id' => $home->id]);
    }
}
