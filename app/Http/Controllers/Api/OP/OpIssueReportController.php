<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OpIssueReportController extends Controller
{
    public function ContactSupportTeam(Request $request)
    {
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
            'user_id' => Auth::id(),
            'origin' => 'user/Contact Support Team',
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Contact sent successfully',
            'response' => $contact
        ], 200);

    }
}
