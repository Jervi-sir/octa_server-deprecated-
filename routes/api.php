<?php

use App\Http\Controllers\Api\OP\OpAuthController;
use App\Http\Controllers\Api\OP\OpBlockController;
use App\Http\Controllers\Api\OP\OpCollectionController;
use App\Http\Controllers\Api\OP\OpConversationController;
use App\Http\Controllers\Api\OP\OpFriendRequestController;
use App\Http\Controllers\Api\OP\OpItemController;
use App\Http\Controllers\Api\OP\OpSearchController;
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
		Route::get('test_my_type', fn() => response()->json(['m i a shop ?' => isAuthShop()]));
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

Route::prefix('octa_prizes/')->group(function () {
	Route::post('register/create', [OpAuthController::class, 'createUser']);
	Route::post('register/send-email', [OpAuthController::class, '']);
	Route::post('register/verify-otp', [OpAuthController::class, '']);
	Route::post('register/verify-username-availability', [OpAuthController::class, 'verifyUsernameAvailability']);
	Route::post('login', [OpAuthController::class, 'loginUser']);



	Route::middleware(['auth:users'])->group(function () {
		Route::get('test_my_type', fn() => response()->json(['m i a shop ?' => isAuthShop()]));
		Route::post('logout', [OpAuthController::class, 'logoutUser']);	//*done
		Route::get('validate_token', [OpAuthController::class, 'validateToken']);	//*done

		Route::prefix('collection')->group(function () {
			Route::get('list', [OpCollectionController::class, 'listCollections']);			//* done
			Route::post('create', [OpCollectionController::class, 'createCollection']);	//* done
			Route::post('add_shop', [OpCollectionController::class, 'saveStoreToCollection']);	//* done
			Route::post('remove_shop', [OpCollectionController::class, 'removeStoreFromCollection']);	//* done
		});

		Route::prefix('conversations')->group(function () {
			Route::get('suggest_friend_to_share_with', [OpConversationController::class, 'suggestFriendToShareWith']);	//* done
			Route::post('send_message_to', [OpConversationController::class, 'sendMessageTo']);

		});

		Route::prefix('item')->group(function () {
			Route::post('report', [OpItemController::class, 'reportItem']);     //* done           
			Route::post('save', [OpItemController::class, 'saveThisItem']);			//* done         
			Route::post('unsave', [OpItemController::class, 'unSaveThisItem']);	//* done
		});

		Route::prefix('friends')->group(function () {
			Route::post('block', [OpBlockController::class, 'blockUser']);
			Route::post('unblock', [OpBlockController::class, 'unblockUser']);
			Route::get('list_blocked', [OpBlockController::class, 'listBlockedUsers']);
		});

		Route::prefix('friend_request')->group(function () {
			Route::post('send', [OpFriendRequestController::class, 'sendRequest']);
			Route::post('accept', [OpFriendRequestController::class, 'acceptRequest']);
			Route::post('reject', [OpFriendRequestController::class, 'rejectRequest']);
			Route::get('received', [OpFriendRequestController::class, 'showReceivedRequests']);
			Route::get('sent', [OpFriendRequestController::class, 'showSentRequests']);
			Route::get('friend-list', [OpFriendRequestController::class, 'showFriendList']);
		});

		Route::prefix('auth')->group(function () {
			RouteItems();
			RouteStores();
			RouteUsers();
		});
	});

	RouteItems();
	RouteStores();
	RouteUsers();

});

function RouteItems()
{
	Route::prefix('items/')->group(function () {
		Route::get('search', [OpSearchController::class, 'search']);

		//Route::get('show/{item_id}', [ShowController::class, 'showItem']);
		//Route::get('suggest', [SearchController::class, 'suggest']);
		//Route::get('category/{category_name}', [SearchController::class, 'byCategory']);
	});
}

function RouteStores()
{
	Route::prefix('shop/')->group(function () {
		Route::get('search', [OpSearchController::class, 'searchShop']);
		Route::get('show/{shopId}', [OpSearchController::class, 'showShop']);
		Route::get('show/{shopId}/{category_name}', [OpSearchController::class, 'showShop']);
	});
}

function RouteUsers()
{
	Route::prefix('profile/')->group(function () {
		Route::get('search', [OpSearchController::class, 'searchProfile']);
		Route::get('show/{userId}', [OpSearchController::class, 'showProfile']);
	});
}

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
								
					Route::get('get_saved', [ActionController::class, 'getSavedItems']);    
			});
			
			Route::prefix('collection')->group(function () {
					Route::get('show', [CollectionController::class, 'getCollectionDetails']);
					Route::post('update', [CollectionController::class, 'updateCollection']);
					Route::post('delete', [CollectionController::class, 'deleteCollection']);
			});
			
			Route::prefix('conversations')->group(function () {
					Route::get('list', [ConversationController::class, 'listConversations']);
					Route::get('show', [ConversationController::class, 'showThisConversation']);
					Route::post('unsend_message', [ConversationController::class, 'unsendMessage']);
			});
			//Route::get('messages/{message}', [ConversationController::class, 'showMessage']);
			//Route::get('show_my_map', [ProfileController::class, 'showMyMap']);
			//Route::get('edit_my_map', [ProfileController::class, 'showMyProfile']);
			//Route::post('update_my_map', [ProfileController::class, 'showMyProfile']);
	});
});
*/

/*-------- Functions --------




*/


