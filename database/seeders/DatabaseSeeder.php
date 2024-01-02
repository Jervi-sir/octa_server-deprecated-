<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Item;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Shop;
use App\Models\User;
use App\Models\CreditTransaction;
use App\Models\Wilaya;
use App\Models\UserMap;
use App\Models\UserSave;
use App\Models\UserUnlock;
use App\Models\ProductType;
use App\Models\PaymentTransaction;
use App\Models\UserFollowing;
use App\Models\PaymentHistory;
use Illuminate\Database\Seeder;
use App\Models\UserShopFollowing;
use Database\Seeders\WilayaSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles
        //Role::factory()->create(['role_name' => 'admin']);

        // Seed wilayas
        /**/
         
        $role = new RoleSeeder();
        $role->run();
        $wilaya = new WilayaSeeder();
        $wilaya->run();
        $product_type = new ProductTypeSeeder();
        $product_type->run();
        User::factory(100)->create();
        Shop::factory(20)->create();
        Item::factory(500)->create();


        
        UserUnlock::factory(200)->create();
        Sale::factory(100)->create();
        UserSave::factory(200)->create();
        
        UserFollowing::factory(200)->create();
        
        UserShopFollowing::factory(200)->create();
        
        CreditTransaction::factory(50)->create();
        PaymentTransaction::factory(350)->create();
    }
}
