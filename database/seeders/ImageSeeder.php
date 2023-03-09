<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 0; $i < 70; $i++) {
            $item_id = rand(0,69);
            $random_img = rand(1, 14);
            DB::table('item_images')->insert([
                'item_id' => $item_id,
                'url' => 'item(' . $random_img . ').png',
                'meta' => 'meta' . $i,
            ]);
        }
    }
}
