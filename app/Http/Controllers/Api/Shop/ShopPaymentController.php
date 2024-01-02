<?php

namespace App\Http\Controllers\Api\Shop;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\CreditTransaction;
use App\Models\PaymentTransaction;

class ShopPaymentController extends Controller
{
    public function sendCredit(Request $request, $user_id)
    {
        $request->validate([
            'amount'   => 'required|numeric',
        ]);
        
        $data = $request->all();
        $user = auth()->user();
        $shop = $user->shop;
        $taget_user = User::find($user_id);
        $amount = floatval($data['amount']);
        
        $shop_credit = $user->credit;
        if($shop_credit < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Not Enough credit in your account',
            ]);
        } else {
            $taget_user->credit += $amount;
            $user->credit -= $amount;

            $credit_transaction = new CreditTransaction();
            $credit_transaction->user_id = $taget_user->id;
            $credit_transaction->shop_id = $shop->id;
            $credit_transaction->amount = $amount;
            $credit_transaction->status = 'completed';
            $credit_transaction->save();

            $taget_user->save();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Transaction made successfully',
            ]);
        }
    }

    public function rechargeMyAccount(Request $request) 
    {
        $request->validate([
            'proof_image'   => 'required',
            'amount'        => 'nullable'
        ]);

        $user = auth()->user();
        $shop = $user->shop;

        $payment_transactions = new PaymentTransaction();
        $payment_transactions->shop_id = $shop->id;
        $payment_transactions->amount = $data['amount'] ?? 0;
        $payment_transactions->type = 'with proof image';
        $payment_transactions->proof_image = $data['proof_image'] ?? null;
        
        $payment_transactions->status = 'pending';
        $payment_transactions->save();

        return response()->json([
            'success' => true,
            'message' => 'Transaction in process, please wait...',
        ]);
    }
    

    public function rechargingHistory(Request $request) 
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $user = auth()->user();
        $shop = $user->shop;

        $payment_transactions = $shop->paymentTransactions()->orderBy('id', 'desc')->paginate(7);
        
        foreach ($payment_transactions as $key => $transaction) {
            $data['transactions'][$key] = [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'type' => $transaction->type,
                'proof_image' => $transaction->proof_image,
                'status' => $transaction->status,
                'updated_at' => $transaction->updated_at,
                'created_at' => $transaction->created_at,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction in process, please wait...',
            'pagination' => [
                'total' => $payment_transactions->total(),
                'per_page' => $payment_transactions->perPage(),
                'current_page' => $payment_transactions->currentPage(),
                'last_page' => $payment_transactions->lastPage(),
                'from' => $payment_transactions->firstItem(),
                'to' => $payment_transactions->lastItem(),
            ],
            'transactions' => $data['transactions']
        ]);
    }
    
    public function creditHistory(Request $request) 
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $user_shop = auth()->user();
        $shop = $user_shop->shop;

        $credit_transactions = $shop->creditTransactions()->orderBy('id', 'desc')->paginate(7);
        
        foreach ($credit_transactions as $key => $credit) {
            $this_user = User::find($credit->user_id);
            $data['credits'][$key] = [
                'id' => $credit->id,
                'amount' => $credit->amount,
                'type' => $credit->type,
                'proof_image' => $credit->proof_image,
                'status' => $credit->status,
                'updated_at' => $credit->updated_at,
                'created_at' => $credit->created_at,
                'user' => [
                    'id' => $this_user->id,
                    'name' => $this_user->name,
                    'username' => $this_user->username,
                    'phone_number' => $this_user->phone_number,
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction in process, please wait...',
            'pagination' => [
                'total' => $credit_transactions->total(),
                'per_page' => $credit_transactions->perPage(),
                'current_page' => $credit_transactions->currentPage(),
                'last_page' => $credit_transactions->lastPage(),
                'from' => $credit_transactions->firstItem(),
                'to' => $credit_transactions->lastItem(),
            ],
            'transactions' => $data['credits']
        ]);
    }
}
