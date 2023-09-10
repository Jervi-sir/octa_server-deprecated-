<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Item;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use App\Models\Wilaya;
use App\Models\UserMap;
use App\Models\UserSave;
use App\Models\PaymentHistory;
use App\Models\ProductType;
use Illuminate\Database\Seeder;
use Database\Seeders\WilayaSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles
        Role::factory()->create(['role_name' => 'admin']);
        Role::factory()->create(['role_name' => 'user']);
        Role::factory()->create(['role_name' => 'assistant']);

        // Seed wilayas
        //Wilaya::factory()->create();
        $wilaya = new WilayaSeeder();
        $wilaya->run();
        
        $product_type = new ProductTypeSeeder();
        $product_type->run();

        // Seed users
        User::factory(50)->create();

        // Seed shops
        Shop::factory(50)->create();

        // Seed user maps
        UserMap::factory(150)->create();

        Item::factory(200)->create();
        UserSave::factory(200)->create();
        PaymentHistory::factory(600)->create();

    }
}
