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

Route::get('/showShop/{shopId}', [ShowController::class, 'showShop']);   //[api]
Route::get('/showUser/{userId}', [ShowController::class, 'showUser']);   //[api]

Route::post('/auth/register',   [AuthController::class, 'createUser']);   //[api]
Route::post('/auth/login',      [AuthController::class, 'loginUser']);       //[api]

Route::get('/suggestShops', [SearchController::class, 'suggestShop']);  //[api]

Route::get('/search/{keywords}', [SearchController::class, 'search']);  //[]

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/showMyProfile', [ProfileController::class, 'showMyProfile']);  //[api]
    Route::get('/suggestItems', [SearchController::class, 'suggest']);          //[api]
    Route::post('/auth/logout', [AuthController::class, 'logoutUser']);         //[api]
    Route::post('/action/saveItem/{itemId}', [ActionController::class, 'saveItem']);    //[api]
    Route::post('/action/unSaveItem/{itemId}', [ActionController::class, 'unSaveItem']); //[api]
    Route::get('/getSavedItems', [ProfileController::class, 'getSavedItems']);  //[api]
});

/*-- not done --*/
Route::post('/shop/register', [ShopController::class, 'createShop']);   //[api]
Route::post('/shop/login', [ShopController::class, 'loginShop']);       //[api]

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/shop/logout', [ShopController::class, 'logoutShop']); //[]
    Route::post('/shop/publish', [ShopController::class, 'publishItem']); //[]
});

//Route::get('/shop&i={id}', [ItemController::class, 'showShop']);    
Route::get('/item/show/{id}', [ShowController::class, 'showItem']);    //[api]
