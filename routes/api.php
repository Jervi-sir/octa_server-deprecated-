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
    Route::post('register', [ShopController::class, 'createShop']);   //[]
    Route::post('login', [ShopController::class, 'loginShop']);       //[verified]

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [ShopController::class, 'logoutShop']); //[verified]
        Route::get('validate_token', [ShopController::class, 'validateToken']);
        Route::get('payment_history', [ShopController::class, 'paymentHistory']);

        Route::post('publish', [ShopController::class, 'publishItem']); //[verified]
        Route::post('repost', [ShopController::class, 'repostItem']); //[verified]
        Route::get('my_store', [ShopController::class, '']); //[]
        Route::get('my_products/{category_name}', [ShopController::class, 'listMyProducts']); //[]
        Route::get('my_products_offset/{category_name}/{start_id}', [ShopController::class, 'listMyProductsWithOffset']); //[]
        
        Route::post('update_pic_name', [ShopController::class, '']); //[]
        Route::post('update_socials', [ShopController::class, '']); //[]
        Route::post('update_bio_location', [ShopController::class, '']); //[]

        Route::post('verify_clients_payeer', [ShopController::class, '']); //[]
        Route::post('send_credit_to/{payeer_account}', [ShopController::class, '']); //[]
        Route::post('recharge_my_account', [ShopController::class, '']); //[]

    });
});

//Route::get('/shop&i={id}', [ItemController::class, 'showShop']);    
Route::get('/item/show/{id}', [ShowController::class, 'showItem']);    //[]

Route::get('/showShop/{shopId}', [ShowController::class, 'showShop']);   //[]
Route::get('/showUser/{userId}', [ShowController::class, 'showUser']);   //[]

Route::post('/auth/register',   [AuthController::class, 'createUser']);   //[]
Route::post('/auth/login',      [AuthController::class, 'loginUser']);       //[]

Route::get('/suggestShops', [SearchController::class, 'suggestShop']);  //[]

Route::get('/search/{keywords}', [SearchController::class, 'search']);  //[]

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/showMyProfile', [ProfileController::class, 'showMyProfile']);  //[]
    Route::get('/suggestItems', [SearchController::class, 'suggest']);          //[]
    Route::post('/auth/logout', [AuthController::class, 'logoutUser']);         //[]
    Route::post('/action/saveItem/{itemId}', [ActionController::class, 'saveItem']);    //[]
    Route::post('/action/unSaveItem/{itemId}', [ActionController::class, 'unSaveItem']); //[]
    Route::get('/getSavedItems', [ProfileController::class, 'getSavedItems']);  //[]
});

