<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            'role_name' => 'admin'
        ]);
        DB::table('roles')->insert([
            'role_name' => 'shop'
        ]);
        DB::table('roles')->insert([
            'role_name' => 'user'
        ]);

    }
}
