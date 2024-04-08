<?php

use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\FriendRequestController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\Shop\ShopAuthController;
use App\Http\Controllers\Api\Shop\ShopItemController;
use App\Http\Controllers\Api\Shop\ShopListItemsController;
use App\Http\Controllers\Api\Shop\ShopProfileController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ShowController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('test', fn() => response()->json('absc'));
Route::prefix('octa_store/')->group(function () {
    Route::post('register', [ShopAuthController::class, 'createShop']); //!not done
    Route::post('login', [ShopAuthController::class, 'loginShop']);     //*Done

    Route::middleware(['auth:shops'])->group(function () {
    Route::post('logout', [ShopAuthController::class, 'logoutShop']);           //*Done
    Route::get('validate_token', [ShopAuthController::class, 'validateToken']); //*Done

    Route::prefix('items')->group(function () {
        Route::post('publish', [ShopItemController::class, 'publishItem']);                 //*Done
        Route::get('item/{item_id}', [ShopItemController::class, '']);                      //*Done 
        Route::get('item_edit/{item_id}', [ShopItemController::class, 'editItem']);         //*Done 
        Route::post('item_update/{item_id}', [ShopItemController::class, 'updateItem']);    //*Done 
        Route::post('item_delete/{item_id}', [ShopItemController::class, 'deleteItem']);    //*Done 
        Route::post('item_repost', [ShopItemController::class, 'repostItem']);              //*Done
        Route::get('category/{category_name}', [ShopListItemsController::class, 'listMyProducts']);//*Done
    });

    Route::prefix('my_store')->group(function () {
        Route::get('show', [ShopController::class, 'myStoreInfo']);                         //*Done
        Route::get('show_my_followers', [ShopController::class, 'showMyFollowers']);        //*Done
        Route::post('update_pic_name', [ShopProfileController::class, 'updatePic_Name']);   //*Done
        Route::post('update_socials', [ShopProfileController::class, 'updateSocialList']);  //*Done
        Route::post('update_description', [ShopProfileController::class, 'updateBio']);     //*Done
        Route::post('update_location', [ShopProfileController::class, 'updateLocation']);   //*Done
    });

    //Route::post ('send_credit_to',       [ShopPaymentController::class, 'sendCredit']);          
    //Route::post ('recharge_my_account',  [ShopPaymentController::class, 'rechargeMyAccount']);   
    //Route::get  ('recharging_history',   [ShopPaymentController::class, 'rechargingHistory']);   
    //Route::get  ('credit_history',       [ShopPaymentController::class, 'creditHistory']);       
    //Route::post ('verify_account',       [ShopPaymentController::class, 'verifyUser']);       
    //Route::post('verify_clients_payeer', [ShopPaymentController::class, '']); 
    //Route::get('payment_history', [ShopProfileController::class, 'paymentHistory']);
    //Route::get('my_products_offset/{category_name}/{start_id}', [ShopListItemsController::class, 'listMyProductsWithOffset']); 
    });
});



Route::prefix('octa_prizes/')->group(function () {
    Route::post('login', [AuthController::class, 'loginUser']);             //*Done
    Route::post('semi-register', [AuthController::class, 'semiCreateUser']);//!not done

    RouteStores();
    RouteUsers();
    RouteItems();

    Route::middleware(['auth:users'])->group(function () {
        Route::prefix('auth_search/')->group(function () {      //*Done
            RouteStores();
            RouteUsers();
            RouteItems();
        });

        Route::post('item/report', [ActionController::class, 'reportItem']);                //*Done

        Route::post('logout', [AuthController::class, 'logoutUser']);                       //*Done
        Route::post('complete-register', [AuthController::class, 'completeCreateUser']);    //!not done

        Route::prefix('profile')->group(function () {
            Route::get('show', [ProfileController::class, 'showMyProfile']);                //*Done
            Route::post('update', [ProfileController::class, 'updateMyProfile']);           //*Done
        });
        Route::prefix('follow')->group(function () {
            Route::post('do', [ActionController::class, 'followUser']);                     //*Done
            Route::post('undo', [ActionController::class, 'unfollowUser']);                 //*Done
            Route::get('get_followings', [ActionController::class, 'getMyFollowings']);     //*Done
            Route::get('get_followers', [ActionController::class, 'getMyFollowers']);       //*Done
        });
        Route::prefix('like_user')->group(function () {
            Route::post('do', [ActionController::class, 'likeUser']);                       //*Done
            Route::post('undo', [ActionController::class, 'unlikeUser']);                   //*Done
            Route::get('list-who-i-liked', [ActionController::class, 'listUsersILiked']);   //*Done
            Route::get('list-who-liked-me', [ActionController::class, 'listLikedByUsers']); //*Done
        });
        Route::prefix('save_item')->group(function () {
            Route::post('do', [ActionController::class, 'saveItem']);               //*Done
            Route::post('undo', [ActionController::class, 'unSaveItem']);           //*Done
            Route::get('get_saved', [ActionController::class, 'getSavedItems']);    //*Done
        });
        Route::prefix('friend_request')->group(function () {
            Route::post('send', [FriendRequestController::class, 'sendRequest']);               //*Done
            Route::post('accept', [FriendRequestController::class, 'acceptRequest']);           //*Done
            Route::post('reject', [FriendRequestController::class, 'rejectRequest']);           //*Done
            Route::get('received', [FriendRequestController::class, 'showReceivedRequests']);   //*Done
            Route::get('sent', [FriendRequestController::class, 'showSentRequests']);           //*Done
            Route::get('friend-list', [FriendRequestController::class, 'showFriendList']);      //*Done
        });
        Route::prefix('collection')->group(function () {
            Route::post('create', [CollectionController::class, 'createCollection']);                //*Done
            Route::get('list', [CollectionController::class, 'listCollections']);                    //*Done
            Route::get('show', [CollectionController::class, 'getCollectionDetails']);               //*Done
            Route::post('update', [CollectionController::class, 'updateCollection']);                //*Done
            Route::post('delete', [CollectionController::class, 'deleteCollection']);                //*Done
            Route::post('add_shop', [CollectionController::class, 'saveStoreToCollection']);         //*Done
            Route::post('remove_store', [CollectionController::class, 'removeStoreFromCollection']); //*Done
        });
        Route::prefix('friends')->group(function () {
            Route::post('block', [BlockController::class, 'blockUser']);                    //*Done
            Route::post('unblock', [BlockController::class, 'unblockUser']);                //*Done
            Route::get('list_blocked', [BlockController::class, 'listBlockedUsers']);       //*Done
        });
        Route::prefix('conversations')->group(function () {
            Route::get('list', [ConversationController::class, 'listConversations']);       //*Done
            Route::get('show', [ConversationController::class, 'showThisConversation']);    //*Done
            Route::post('send_message_to', [ConversationController::class, 'storeMessage']);//*Done
            Route::post('unsend_message', [ConversationController::class, 'unsendMessage']);//*Done
            Route::get('suggest_friend_to_share_with', [ActionController::class, 'suggestFriendToShareWith']);//[]
        });
        //Route::get('messages/{message}', [ConversationController::class, 'showMessage']);
        //Route::get('show_my_map', [ProfileController::class, 'showMyMap']);             //[]
        //Route::get('edit_my_map', [ProfileController::class, 'showMyProfile']);         //[]
        //Route::post('update_my_map', [ProfileController::class, 'showMyProfile']);      //[]
    });
});

/*-------- Functions --------*/
 function RouteItems() {
    Route::prefix('item/')->group(function () {
        Route::get('search', [SearchController::class, 'search']);          //[]
        Route::get('show/{item_id}', [ShowController::class, 'showItem']);  //[done]
        Route::get('suggest', [SearchController::class, 'suggest']);        //[done]
        Route::get('category/{category_name}', [SearchController::class, 'byCategory']);//[done]
    });
}
function RouteStores() {
    Route::prefix('store/')->group(function () {
        Route::get('search', [SearchController::class, 'searchShop']);                      //[]
        Route::get('show/{shopId}', [ShowController::class, 'showShop']);                   //[done]
        Route::get('show/{shopId}/{category_name}', [ShowController::class, 'showShop']);   //[done]
    });
}
function RouteUsers() {
    Route::prefix('user/')->group(function () {
        Route::get('search', [SearchController::class, 'searchProfile']);   //[]
        Route::get('show/{userId}', [ShowController::class, 'showUser']);   //[done]
    });
}

