<?php

use App\Http\Controllers\Api\Shop\ShopAuthController;
use App\Http\Controllers\Api\Shop\ShopContactController;
use App\Http\Controllers\Api\Shop\ShopItemController;
use App\Http\Controllers\Api\Shop\ShopListItemsController;
use App\Http\Controllers\Api\Shop\ShopProfileController;
use App\Http\Controllers\Api\Shop\ShopSocialController;
use App\Http\Controllers\Api\Shop\ShopUpdateProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
	return $request->user();
});

Route::get('test', fn() => response()->json('absc'));

Route::prefix('octa_store/')->group(function () {
	Route::post('register', [ShopAuthController::class, 'createShop']);     			//*done
	Route::post('login', [ShopAuthController::class, 'loginShop']);         			//*done

	Route::middleware(['auth:shops'])->group(function () {
		Route::post('logout', [ShopAuthController::class, 'logoutShop']);       		//*done
		Route::get('validate_token', [ShopAuthController::class, 'validateToken']);	//*done
		Route::post('submit_contact_us', [ShopContactController::class, 'storeSendsContactSupport']);//*done             

		Route::prefix('items')->group(function () {
			Route::post('publish', [ShopItemController::class, 'publishItem']); 							//*done
			Route::post('item_repost', [ShopItemController::class, 'repostItem']); 						//*done
			Route::get('item_edit/{item_id}', [ShopItemController::class, 'editItem']); 			//*done
			Route::post('item_update/{item_id}', [ShopItemController::class, 'updateItem']); 	//*done
			Route::post('item_delete/{item_id}', [ShopItemController::class, 'deleteItem']);	//*done
			//Route::get('item/{item_id}', [ShopItemController::class, '']);                      
			Route::get('category/{category_name}', [ShopListItemsController::class, 'listMyProducts']);//*done
		});

		Route::prefix('my_store')->group(function () {
			Route::get('show', [ShopProfileController::class, 'myStoreInfo']);									//*done   
			Route::get('show_my_followers', [ShopProfileController::class, 'showMyFollowers']);	//*done   
			Route::post('update_profile', [ShopUpdateProfileController::class, 'updateShopProfile']); //*done 

			Route::post('update_this_social', [ShopSocialController::class, 'updateThisSocial']);    
			Route::post('add_socials', [ShopSocialController::class, 'addSocials']);           
			Route::post('delete_this_social', [ShopSocialController::class, 'deleteThisSocial']);           
		});
	});

});




//Route::post ('send_credit_to',       [ShopPaymentController::class, 'sendCredit']);          
//Route::post ('recharge_my_account',  [ShopPaymentController::class, 'rechargeMyAccount']);   
//Route::get  ('recharging_history',   [ShopPaymentController::class, 'rechargingHistory']);   
//Route::get  ('credit_history',       [ShopPaymentController::class, 'creditHistory']);       
//Route::post ('verify_account',       [ShopPaymentController::class, 'verifyUser']);       
//Route::post('verify_clients_payeer', [ShopPaymentController::class, '']); 
//Route::get('payment_history', [ShopProfileController::class, 'paymentHistory']);
//Route::get('my_products_offset/{category_name}/{start_id}', [ShopListItemsController::class, 'listMyProductsWithOffset']); 

/*
Route::prefix('octa_prizes/')->group(function () {
	Route::post('login', [AuthController::class, 'loginUser']);             
	Route::post('semi-register', [AuthController::class, 'semiCreateUser']);

	RouteStores();
	RouteUsers();
	RouteItems();

	Route::middleware(['auth:users'])->group(function () {
			Route::prefix('auth_search/')->group(function () {      
					RouteStores();
					RouteUsers();
					RouteItems();
			});

			Route::post('item/report', [ActionController::class, 'reportItem']);                

			Route::post('logout', [AuthController::class, 'logoutUser']);                       
			Route::post('complete-register', [AuthController::class, 'completeCreateUser']);    

			Route::prefix('profile')->group(function () {
					Route::get('show', [ProfileController::class, 'showMyProfile']);                
					Route::post('update', [ProfileController::class, 'updateMyProfile']);           
			});
			Route::prefix('follow')->group(function () {
					Route::post('do', [ActionController::class, 'followUser']);                     
					Route::post('undo', [ActionController::class, 'unfollowUser']);                 
					Route::get('get_followings', [ActionController::class, 'getMyFollowings']);     
					Route::get('get_followers', [ActionController::class, 'getMyFollowers']);       
			});
			Route::prefix('like_user')->group(function () {
					Route::post('do', [ActionController::class, 'likeUser']);                       
					Route::post('undo', [ActionController::class, 'unlikeUser']);                   
					Route::get('list-who-i-liked', [ActionController::class, 'listUsersILiked']);   
					Route::get('list-who-liked-me', [ActionController::class, 'listLikedByUsers']); 
			});
			Route::prefix('save_item')->group(function () {
					Route::post('do', [ActionController::class, 'saveItem']);               
					Route::post('undo', [ActionController::class, 'unSaveItem']);           
					Route::get('get_saved', [ActionController::class, 'getSavedItems']);    
			});
			Route::prefix('friend_request')->group(function () {
					Route::post('send', [FriendRequestController::class, 'sendRequest']);               
					Route::post('accept', [FriendRequestController::class, 'acceptRequest']);           
					Route::post('reject', [FriendRequestController::class, 'rejectRequest']);           
					Route::get('received', [FriendRequestController::class, 'showReceivedRequests']);   
					Route::get('sent', [FriendRequestController::class, 'showSentRequests']);           
					Route::get('friend-list', [FriendRequestController::class, 'showFriendList']);      
			});
			Route::prefix('collection')->group(function () {
					Route::post('create', [CollectionController::class, 'createCollection']);
					Route::get('list', [CollectionController::class, 'listCollections']);
					Route::get('show', [CollectionController::class, 'getCollectionDetails']);
					Route::post('update', [CollectionController::class, 'updateCollection']);
					Route::post('delete', [CollectionController::class, 'deleteCollection']);
					Route::post('add_shop', [CollectionController::class, 'saveStoreToCollection']);
					Route::post('remove_store', [CollectionController::class, 'removeStoreFromCollection']);
			});
			Route::prefix('friends')->group(function () {
					Route::post('block', [BlockController::class, 'blockUser']);
					Route::post('unblock', [BlockController::class, 'unblockUser']);
					Route::get('list_blocked', [BlockController::class, 'listBlockedUsers']);
			});
			Route::prefix('conversations')->group(function () {
					Route::get('list', [ConversationController::class, 'listConversations']);
					Route::get('show', [ConversationController::class, 'showThisConversation']);
					Route::post('send_message_to', [ConversationController::class, 'storeMessage']);
					Route::post('unsend_message', [ConversationController::class, 'unsendMessage']);
					Route::get('suggest_friend_to_share_with', [ActionController::class, 'suggestFriendToShareWith']);
			});
			//Route::get('messages/{message}', [ConversationController::class, 'showMessage']);
			//Route::get('show_my_map', [ProfileController::class, 'showMyMap']);
			//Route::get('edit_my_map', [ProfileController::class, 'showMyProfile']);
			//Route::post('update_my_map', [ProfileController::class, 'showMyProfile']);
	});
});
*/

/*-------- Functions --------
function RouteItems() {
	Route::prefix('item/')->group(function () {
			Route::get('search', [SearchController::class, 'search']);
			Route::get('show/{item_id}', [ShowController::class, 'showItem']);
			Route::get('suggest', [SearchController::class, 'suggest']);
			Route::get('category/{category_name}', [SearchController::class, 'byCategory']);
	});
}
function RouteStores() {
	Route::prefix('store/')->group(function () {
			Route::get('search', [SearchController::class, 'searchShop']);
			Route::get('show/{shopId}', [ShowController::class, 'showShop']);
			Route::get('show/{shopId}/{category_name}', [ShowController::class, 'showShop']);
	});
}
function RouteUsers() {
	Route::prefix('user/')->group(function () {
			Route::get('search', [SearchController::class, 'searchProfile']);
			Route::get('show/{userId}', [ShowController::class, 'showUser']);
	});
}

*/


