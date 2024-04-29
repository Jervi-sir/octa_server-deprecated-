<?php

namespace Database\Seeders;

use App\Models\Friend;
use App\Models\Item;
use App\Models\Shop;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserLike;
use App\Models\UserSave;
use App\Models\UserShopFollowing;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles
        //Role::factory()->create(['role_name' => 'admin']);

        //User::factory(50)->create();
        //Friend::factory(50)->create();
        UserLike::factory(50)->create();

        //Country
        //$country = new CountrySeeder();
        //$country->run();
        //Wilaya
        //$wilaya = new WilayaSeeder();
        //$wilaya->run();
        //Item Type
        //$itemType = new ItemTypeSeeder();
        //$itemType->run();
        //Activation Code
        //$activationCode = new ActivationCodeSeeder();
        //$activationCode->run();

    }
}
         



//$role = new RoleSeeder();
//$role->run();
//$wilaya = new WilayaSeeder();
//$wilaya->run();
//$product_type = new ProductTypeSeeder();
//$product_type->run();
//User::factory(100)->create();
//Shop::factory(20)->create();

//Item::factory(500)->create();

//UserUnlock::factory(200)->create();
//Sale::factory(100)->create();
//UserSave::factory(200)->create();

//UserFollowing::factory(200)->create();
//UserShopFollowing::factory(200)->create();

//CreditTransaction::factory(50)->create();
//PaymentTransaction::factory(350)->create();