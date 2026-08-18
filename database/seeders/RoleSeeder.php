<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate([
            'slug' => 'admin'
        ], [
            'name' => 'Administrator',
            'description' => 'Full access to all system features.'
        ]);

        $customerRole = Role::firstOrCreate([
            'slug' => 'customer'
        ], [
            'name' => 'Customer',
            'description' => 'Standard customer with access to shop and orders.'
        ]);

        // Create dummy Admin
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        
        if (!$adminUser->hasRole('admin')) {
            $adminUser->roles()->attach($adminRole->id);
        }

        // Create dummy Customer
        $customerUser = User::firstOrCreate([
            'email' => 'customer@example.com'
        ], [
            'name' => 'Customer User',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        
        if (!$customerUser->hasRole('customer')) {
            $customerUser->roles()->attach($customerRole->id);
        }
    }
}
