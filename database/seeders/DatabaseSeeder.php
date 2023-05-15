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
use Illuminate\Database\Seeder;

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
        for ($i = 1; $i < 48; $i++) {
            Wilaya::factory()->create(['id' => $i, 'code' => $i]);
        }

        // Seed users
        $users = User::factory(150)->create();

        // Seed shops
        $shops = Shop::factory(600)->create();

        // Seed user maps
        UserMap::factory(150)->create();

        // Seed user saves
        $item = Item::factory(2000)->create();
        UserSave::factory(1000)->create();
    }
}
