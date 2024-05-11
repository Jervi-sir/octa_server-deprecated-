<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\DistributorStar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistributionController extends Controller
{
    public function generateActivationCode(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'distributor_id' => 'sometimes',
            'count' => 'sometimes',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validate->errors()
            ], 401);
        }

        $count = $request->count;
        $distributor = DistributorStar::find($request->distributor_id);
        for ($i=0; $i < $count; $i++) { 
            $activation_code = new ActivationCode();
            $activation_code->code = ActivationCode::generate();
            $activation_code->distributor_star_id = $distributor->id;
            $activation_code->status = 'new';
            $activation_code->save();
        }

        return response()->json([
            'message' => 'created'
        ]);
    }

    public function createDistributor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'wilaya_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 401);
        }

        $distributor = new DistributorStar();
        $distributor->identification = $request->wilaya_id . ActivationCode::generate(4);
        $distributor->name = $request->name;
        $distributor->wilaya_id = $request->wilaya_id;
        $distributor->save();


        $count = 100;
        for ($i=0; $i < $count; $i++) { 
            $activation_code = new ActivationCode();
            $activation_code->code = ActivationCode::generate();
            $activation_code->distributor_star_id = $distributor->id;
            $activation_code->status = 'new';
            $activation_code->save();
        }

        return response()->json([
            'message' => $distributor
        ]);
    }


    public function showMyDistributorProfile(Request $request)
    {
        // $auth = 
        $validator = Validator::make($request->all(), [
            'distributor_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 401);
        }

        $distributor = DistributorStar::find($request->distributor_id);
        $data['distributor'] = [
            'identification' => $request->identification,
            'name' => $request->name,
            'score' => $request->score,
        ];

        $data['activation_codes'] = [];
        foreach ($distributor->rls_activationCode()->where('isUsed', '0')->get() as $index => $activation_code) {
            $data['activation_codes'][$index] = [
                'code' => $activation_code->code,
                'isUsed' => $activation_code->isUsed,
                'distributor_star_id' => $activation_code->distributor_star_id,
                'status' => $activation_code->status,
            ];
        }
        
        return response()->json([
            'message' => 'here are the distributor',
            'distributor' => $data['distributor'],
            'activation_codes' => $data['activation_codes'],
        ]);
    }

    public function reserveThisActivationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'distributor_id' => 'required',
            'activation_code_id' => 'required',
            'activation_code' => 'sometimes',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 401);
        }

        $distributor = DistributorStar::find($request->distributor_id);
        $activation_code = $distributor->rls_activationCode()->where('id', $request->activation_code_id)->first();
        if(!$activation_code){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 401);
        }
                
        $activation_code->status = 'on hold';
        $activation_code->save();

        return response()->json([
            'message' => 'activation code on hold',
            'activation_code' => $activation_code
        ]);
    }
}
