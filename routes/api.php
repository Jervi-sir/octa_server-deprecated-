<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ShopController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/shop/register', [ShopController::class, 'createShop']);
Route::post('/shop/login', [ShopController::class, 'loginShop']);

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/auth/logout', [AuthController::class, 'logoutUser']);
});

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/shop/logout', [ShopController::class, 'logoutShop']);
    Route::post('/shop/publish', [ShopController::class, 'publishItem']);
});

Route::get('/shop&i={id}', [ItemController::class, 'showShop']);
Route::get('/item&i={id}', [ItemController::class, 'showItem']);

Route::get('/search/', [SearchController::class, 'search']);

