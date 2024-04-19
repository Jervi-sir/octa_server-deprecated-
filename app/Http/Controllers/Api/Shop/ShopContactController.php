<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShopContactController extends Controller
{
    public function storeSendsContactSupport(Request $request) {
        $validateUser = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        $contact = Contact::create([
            'shop_id' => Auth::id(),
            'origin' => 'store',
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Contact sent successfully',
            'contact' => $contact
        ], 200);

    }
}
