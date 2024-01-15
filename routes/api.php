<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ShowController;
use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Shop\ShopAuthController;
use App\Http\Controllers\Api\Shop\ShopItemController;
use App\Http\Controllers\Api\Shop\ShopPaymentController;
use App\Http\Controllers\Api\Shop\ShopProfileController;
use App\Http\Controllers\Api\Shop\ShopListItemsController;
use App\Http\Controllers\Api\FriendRequestController;
use App\Http\Controllers\Api\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
/*-- Shop --*/
Route::get('test', fn() => response()->json('absc'));    
Route::prefix('shop/')->group(function() {
    //Route::post ('register', [ShopAuthController::class, 'createShop']);                                     //[]
    Route::post ('login',    [ShopAuthController::class, 'loginShop']);                                      //[V]
    
    Route::middleware(['auth:shops'])->group(function () {
        //Route::get('validate_token', [ShopAuthController::class, 'validateToken']); //[x]
        Route::post ('logout', [ShopAuthController::class, 'logoutShop']);                                  //[V]

        Route::post ('publish_item',            [ShopItemController::class, 'publishItem']);                //[V]
        Route::get  ('edit_item/{item_id}',     [ShopItemController::class, 'editItem']);                   //[V]
        Route::post ('update_item/{item_id}',   [ShopItemController::class, 'updateItem']);                 //[V]
        Route::post ('delete_item/{item_id}',   [ShopItemController::class, 'deleteItem']);                 //[V]
        
        Route::get  ('my_store',                    [ShopController::class,         'myStoreInfo']);        //[V]
        Route::get  ('show_my_followers',           [ShopController::class,         'showMyFollowers']);    //[V]
        Route::get  ('my_products/{category_name}', [ShopListItemsController::class,'listMyProducts']);     //[V]
        Route::get  ('show_product/{product_id}',   [ShopListItemsController::class,'']);                   //[ ]
        Route::post ('repost',                      [ShopItemController::class,     'repostItem']);         //[V]
        
        Route::post ('update_pic_name',      [ShopProfileController::class, 'updatePic_Name']);              //[V]
        Route::post ('update_socials',       [ShopProfileController::class, 'updateSocialList']);            //[V]
        Route::post ('update_description',   [ShopProfileController::class, 'updateBio']);                   //[V]
        Route::post ('update_location',      [ShopProfileController::class, 'updateLocation']);              //[V]
        
        //Route::post ('send_credit_to',       [ShopPaymentController::class, 'sendCredit']);          //[V]
        //Route::post ('recharge_my_account',  [ShopPaymentController::class, 'rechargeMyAccount']);   //[V]
        //Route::get  ('recharging_history',   [ShopPaymentController::class, 'rechargingHistory']);   //[V]
        //Route::get  ('credit_history',       [ShopPaymentController::class, 'creditHistory']);       //[V]
        //Route::post ('verify_account',       [ShopPaymentController::class, 'verifyUser']);       //[V]
        //Route::post('verify_clients_payeer', [ShopPaymentController::class, '']); //[X]
        //Route::get('payment_history', [ShopProfileController::class, 'paymentHistory']);
        //Route::get('my_products_offset/{category_name}/{start_id}', [ShopListItemsController::class, 'listMyProductsWithOffset']); //[X]
    });
}); 


Route::prefix('auth/')->group(function() {
    Route::post ('semi-register',   [AuthController::class,  'semiCreateUser']);    //[V]
    Route::post ('login',       [AuthController::class,  'loginUser']);     //[V]

    Route::middleware(['auth:users'])->group(function () {
        Route::post ('logout',  [AuthController::class, 'logoutUser']);         //[V]
        Route::post ('complete-register',   [AuthController::class,  'completeCreateUser']);    //[V]

        Route::get  ('show_my_profile',     [ProfileController::class, 'showMyProfile']);   //[V]
        Route::get  ('edit_my_profile',     [ProfileController::class, 'showMyProfile']);   //[V]
        Route::post ('update_my_profile',   [ProfileController::class, 'updateMyProfile']);   //[]
        Route::get  ('show_my_map',         [ProfileController::class, 'showMyMap']);       //[V]
        Route::get  ('edit_my_map',         [ProfileController::class, 'showMyProfile']);   //[X]
        Route::post ('update_my_map',       [ProfileController::class, 'showMyProfile']);   //[X]

        Route::post ('follow_user',          [ActionController::class, 'followUser']);      //[V]
        Route::post ('un_follow_user',       [ActionController::class, 'unfollowUser']);    //[V]
        Route::get  ('get_followings',  [ActionController::class, 'getMyFollowings']);      //[V]
        Route::get  ('get_followers',   [ActionController::class, 'getMyFollowers']);       //[V]

        Route::get  ('get_saved_items', [ActionController::class,  'getSavedItems']); //[V]          //[]
        Route::post ('save_item',       [ActionController::class,  'saveItem']);     //[V]
        Route::post ('un_save_item',    [ActionController::class,  'unSaveItem']);   //[V]

        //Shares side
        Route::post ('share_item',  [ActionController::class,  'shareItem']);     //[ ]
        Route::get ('suggest_friend_to_share_with',  [ActionController::class,  'suggestFriendToShareWith']);     //[ ]
    
        //Friends side
        Route::post('/friend-request/send', [FriendRequestController::class, 'sendRequest']);       //[V]
        Route::post('/friend-request/accept', [FriendRequestController::class, 'acceptRequest']);
        Route::get('/friend-requests/received', [FriendRequestController::class, 'showReceivedRequests']);   //[V]
        Route::get('/friend-requests/sent', [FriendRequestController::class, 'showSentRequests']);  //[V]
        Route::get('/friend-list', [FriendRequestController::class, 'showFriendList']);     //[V]
        
        Route::post('/chat/start', [ChatController::class, 'startChat']);
        Route::get('/chat/list/{userId}', [ChatController::class, 'listChats']);
        Route::get('/chat/details/{chatId}', [ChatController::class, 'getChatDetails']);
        Route::post('/chat/share-item', [ChatController::class, 'shareItem']);
    });
}); 

RouteStores();
RouteUsers();
RouteItems();

Route::prefix('auth/')->middleware(['auth:users'])->group(function () {
    RouteStores();
    RouteUsers();
    RouteItems();
});


/*-------- Functions --------*/

function RouteStores() {
    Route::prefix('store/')->group(function() {
        Route::get('show/{shopId}',                 [ShowController::class,  'showShop']);      //[V]
        Route::get('show/{shopId}/{category_name}',   [ShowController::class,  'showShop']);    //[V]
        Route::get('search',                       [SearchController::class,'searchShop']);   //[]
    });
}
function RouteUsers() {
    Route::prefix('user/')->group(function() {
        Route::get('show/{userId}',     [ShowController::class,  'showUser']);      //[V]
        Route::get('search', [SearchController::class,'searchProfile']);   //[]
    }); 
}

function RouteItems() {
    Route::prefix('item/')->group(function() {
        Route::get('search',           [SearchController::class,   'search']);         //[V]
        Route::get('show/{item_id}',    [ShowController::class,     'showItem']);       //[V]
        Route::get('suggest',           [SearchController::class,   'suggest']);        //[V]
        Route::get('category/{category_name}', [SearchController::class,   'byCategory']);        //[V]
    }); 
}
