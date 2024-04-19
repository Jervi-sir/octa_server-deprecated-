<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itemTypes = [
            [ 'id' => 1,    'name' => 'Hat',       'code'=> 'hat'    ],
            [ 'id' => 2,    'name' => 'Jacket',    'code'=> 'jacket' ],
            [ 'id' => 3,    'name' => 'Pant',      'code'=> 'pant'   ],
            [ 'id' => 4,    'name' => 'Shoes',     'code'=> 'shoes'  ],
            [ 'id' => 5,    'name' => 'Shirt',     'code'=> 'shirt'  ]
        ];

        foreach ($itemTypes  as $key => $value) {
            DB::table('item_types')->insert([
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
            ]);
        }
    }
}
