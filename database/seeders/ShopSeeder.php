<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilaya_number = array(46, 31, 13, 16, 17);
        $wilaya = array('ain temouchent', 'oran', 'tlemcen', 'alger', 'djelfa');
        
        for($i = 0; $i < 10; $i++) {
            $randomNumber = rand(0, 4);
            DB::table('shops')->insert([
                'email' => 'email' . $i . '@gmail.com',
                'password' => Hash::make('password'),
                'password_plainText' => 'password',
                'shop_name' => 'shop_name' . $i ,
                'details' => 'details',
                'contacts' => 'contacts',
                'location' => 'location',
                'map_location' => 'map_location',
                'shop_image' => 'shop(' . ($i + 1) . ').png',
                'wilaya_id' => $wilaya_number[$randomNumber],
                'wilaya_name' => $wilaya[$randomNumber],
            ]);
        }

    }
}
