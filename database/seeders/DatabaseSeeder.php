<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShopSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\WilayaSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        //$roles = new RoleSeeder();
        //$roles->run();

        $roles = new RoleSeeder();
        $wilaya = new WilayaSeeder();
        $shops = new ShopSeeder();
        $items = new ItemSeeder();
        $images = new ImageSeeder();

        $roles->run();
        $wilaya->run();
        $shops->run();
        $items->run();
        $images->run();


    }
}
