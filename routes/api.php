<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ShowController;
use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Shop\ShopAuthController;
use App\Http\Controllers\Api\Shop\ShopItemController;
use App\Http\Controllers\Api\Shop\ShopPaymentController;
use App\Http\Controllers\Api\Shop\ShopProfileController;
use App\Http\Controllers\Api\Shop\ShopListItemsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
/*-- Shop --*/
Route::get('test', function() {
    return getGenderId(["male", "female"]);
});

Route::prefix('shop/')->group(function() {
    Route::post('register', [ShopAuthController::class, 'createShop']);                                     //[]
    Route::post('login',    [ShopAuthController::class, 'loginShop']);                                      //[V]

    Route::middleware('auth:sanctum')->group(function () {
        Route::post ('logout', [ShopAuthController::class, 'logoutShop']);                                  //[V]

        Route::post ('publish_item',            [ShopItemController::class, 'publishItem']);                //[V]
        Route::get  ('edit_item/{item_id}',     [ShopItemController::class, 'editItem']);                   //[V]
        Route::post ('update_item/{item_id}',   [ShopItemController::class, 'updateItem']);                 //[V]
        Route::delete('delete_item/{item_id}',  [ShopItemController::class, 'deleteItem']);                 //[V]
        
        Route::get  ('my_store',                    [ShopController::class,         'myStoreInfo']);        //[V]
        Route::get  ('show_my_followers',           [ShopController::class,         'showMyFollowers']);    //[V]
        Route::get  ('my_products/{category_name}', [ShopListItemsController::class,'listMyProducts']);     //[V]
        Route::get  ('show_product/{product_id}',   [ShopListItemsController::class,'']);                   //[ ]
        Route::post ('repost',                      [ShopItemController::class,     'repostItem']);         //[V]
        
        Route::post('update_pic_name',      [ShopProfileController::class, 'updatePic_Name']);              //[V]
        Route::post('update_socials',       [ShopProfileController::class, 'updateSocialList']);            //[V]
        Route::post('update_description',   [ShopProfileController::class, 'updateBio']);                   //[V]
        Route::post('update_location',      [ShopProfileController::class, 'updateLocation']);              //[V]
        
        Route::post ('send_credit_to/{user_id}',    [ShopPaymentController::class, 'sendCredit']);          //[V]
        Route::post ('recharge_my_account',         [ShopPaymentController::class, 'rechargeMyAccount']);   //[V]
        Route::get  ('recharging_history',          [ShopPaymentController::class, 'rechargingHistory']);   //[V]
        Route::get  ('credit_history',              [ShopPaymentController::class, 'creditHistory']);       //[V]
        
        //Route::post('verify_clients_payeer', [ShopPaymentController::class, '']); //[X]
        //Route::get('validate_token', [ShopAuthController::class, 'validateToken']); //[x]
        //Route::get('payment_history', [ShopProfileController::class, 'paymentHistory']);
        //Route::get('my_products_offset/{category_name}/{start_id}', [ShopListItemsController::class, 'listMyProductsWithOffset']); //[X]

    
    });
}); 

//Route::get('/shop&i={id}', [ItemController::class, 'showShop']);    
Route::get  ('/item/show/{id}',     [ShowController::class,  'showItem']);      //[]

Route::get  ('/showShop/{shopId}',  [ShowController::class,  'showShop']);      //[]
Route::get  ('/showUser/{userId}',  [ShowController::class,  'showUser']);      //[]

Route::post ('/auth/register',      [AuthController::class,  'createUser']);    //[]
Route::post ('/auth/login',         [AuthController::class,  'loginUser']);     //[]

Route::get  ('/suggestShops',       [SearchController::class,'suggestShop']);   //[]

Route::get  ('/search/{keywords}',  [SearchController::class,'search']);        //[]

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout',     [AuthController::class, 'logoutUser']);         //[]
    Route::get('/showMyProfile',    [ProfileController::class, 'showMyProfile']);   //[]
    Route::get('/suggestItems',     [SearchController::class, 'suggest']);          //[]
    Route::post('/action/saveItem/{itemId}',    [ActionController::class, 'saveItem']);     //[]
    Route::post('/action/unSaveItem/{itemId}',  [ActionController::class, 'unSaveItem']);   //[]
    Route::get('/getSavedItems',    [ProfileController::class, 'getSavedItems']);           //[]
});

