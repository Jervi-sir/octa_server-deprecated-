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
    Route::post('register', [ShopAuthController::class, 'createShop']);   //[]
    Route::post('login', [ShopAuthController::class, 'loginShop']);       //[verified]

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [ShopAuthController::class, 'logoutShop']); //[verified]
        Route::get('validate_token', [ShopAuthController::class, 'validateToken']);
        Route::get('payment_history', [ShopProfileController::class, 'paymentHistory']);

        Route::post('publish', [ShopItemController::class, 'publishItem']); //[verified]
        Route::get('edit_item/{item_id}', [ShopItemController::class, 'editItem']); //[verified]
        Route::post('update_item/{item_id}', [ShopItemController::class, 'updateItem']); //[verified]
        Route::post('delete_item/{item_id}', [ShopItemController::class, 'deleteItem']); //[verified]
        
        Route::post('repost', [ShopItemController::class, 'repostItem']); //[verified]
        Route::get('my_store', [ShopController::class, '']); //[]
        Route::get('my_products/{category_name}', [ShopListItemsController::class, 'listMyProducts']); //[verified]
        Route::get('my_products_offset/{category_name}/{start_id}', [ShopListItemsController::class, 'listMyProductsWithOffset']); //[verified]
        
        Route::post('update_pic_name', [ShopProfileController::class, 'updatePic_Name']); //[verified]
        Route::post('update_socials', [ShopProfileController::class, 'updateSocialList']); //[]
        Route::post('update_description', [ShopProfileController::class, 'updateBio']); //[verified]
        Route::post('update_location', [ShopProfileController::class, 'updateLocation']); //[verified]
        
        Route::post('verify_clients_payeer', [ShopPaymentController::class, '']); //[]
        Route::post('send_credit_to/{payeer_account}', [ShopPaymentController::class, '']); //[]
        Route::post('recharge_my_account', [ShopPaymentController::class, '']); //[]
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

