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
use Illuminate\Database\Seeder;
use Database\Seeders\WilayaSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //PaymentHistory::factory(600)->create();
        


        /*
        // Seed roles
        Role::factory()->create(['role_name' => 'admin']);
        Role::factory()->create(['role_name' => 'user']);
        Role::factory()->create(['role_name' => 'assistant']);

        // Seed wilayas
        //Wilaya::factory()->create();
        $wilaya = new WilayaSeeder();
        $wilaya->run();
        
        // Seed users
        $users = User::factory(50)->create();

        // Seed shops
        $shops = Shop::factory(50)->create();

        // Seed user maps
        UserMap::factory(150)->create();

        // Seed user saves
        
        */
        $item = Item::factory(200)->create();
        UserSave::factory(200)->create();
    }
}
