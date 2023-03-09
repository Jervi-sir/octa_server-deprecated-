<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WilayaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilaya_number = array(46, 31, 13, 16, 17);
        $wilaya = array('ain temouchent', 'oran', 'tlemcen', 'alger', 'djelfa');
        
        for($i = 0; $i < 5; $i++) {
           
            DB::table('wilayas')->insert([
                'id' => $wilaya_number[$i],
                'name' => $wilaya[$i],
                'code' => $wilaya_number[$i],
            ]);
        }
    }
}
