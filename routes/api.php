<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ShopController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/showShop/{shopId}', [ProfileController::class, 'showShop']);
Route::get('/showUser/{userId}', [ProfileController::class, 'showUser']);
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::get('/suggest', [SearchController::class, 'suggest']);

Route::get('/search/{keywords}', [SearchController::class, 'search']);



/*-- not done --*/
Route::post('/shop/register', [ShopController::class, 'createShop']);
Route::post('/shop/login', [ShopController::class, 'loginShop']);

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/auth/logout', [AuthController::class, 'logoutUser']);
});

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/shop/logout', [ShopController::class, 'logoutShop']);
    Route::post('/shop/publish', [ShopController::class, 'publishItem']);
});

Route::get('/shop&i={id}', [ItemController::class, 'showShop']);
Route::get('/item&i={id}', [ItemController::class, 'showItem']);




