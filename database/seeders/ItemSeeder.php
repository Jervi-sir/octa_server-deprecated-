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
        $wilaya_number = array(46, 31, 13, 16, 17);
        $wilaya = array('ain temouchent', 'oran', 'tlemcen', 'alger', 'djelfa');

        for($i = 0; $i < 70; $i++) {
            $gender = ['_male', '_female', '_male/_female'];
            $select_gender = rand(0,2);
            $type = ['hat', 'shirt', 'pant', 'shoe', 'watch'];
            $select_type = rand(0,4);
            $randomNumber = rand(0, 4);

            DB::table('items')->insert([
                'shop_id' => rand(1, 10),
                'details' => 'details' . $i,
                'name' => 'name' . $i,
                'sizes' => 'small',
                'stock' => rand(1, 50),
                'price' => 'price' . $i,
                'item_type_id' => $select_type,
                'gender_id' => $select_gender,
                'search' => $this->searchKeyword($gender[$select_gender], $wilaya_number[$randomNumber], $wilaya[$randomNumber], 'details' . $i, 'name' . $i, $type[$select_type] )
            ]);
        }
    }

    private function searchKeyword( $gender, $wilaya_number, $wilaya,  $details, $name, $item_type)
    {
        $keyword =  $name . ' , ' . 
                    $item_type . ' , ' .
                    $wilaya_number . ' , ' .
                    $wilaya . ' , ' .
                    $details . ' , ' .
                    $gender . ' , ';
        return $keyword;
    }
}
