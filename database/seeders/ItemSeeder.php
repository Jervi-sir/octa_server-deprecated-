<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type = array_rand(array("hat", "shirt", "shoes", "watch", "pant"));
        DB::table('items')->insert([
            'shop_id' => 5,
            'shop_name' => 'name',
            'shop_image' => '',
            'details' => '',
            'contacts' => '',
            'map_location' => '',
            'name' => 'name',
            'item_images' => '',
            'size' => 'small',
            'stock' => 10,
            'price' => 'rpice',
            'type' => $type,
        ]);
    }
}
