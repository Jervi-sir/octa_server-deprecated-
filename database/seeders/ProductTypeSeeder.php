<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $algerianWilayas = [
            [ 
              'id' => 1,
              'name' => 'Hat'
            ],
            [
              'id' => 2, 
              'name' => 'Shirt'
            ],
            [
              'id' => 3,
              'name' => 'Pant'
            ],
            [
              'id' => 4,
              'name' => 'Shoes'
            ]
        ];

        foreach ($algerianWilayas  as $key => $value) {
            DB::table('product_types')->create([
                'id' => $value['id'],
                'name' => $value['name'],
            ]);
        }
    }
}
