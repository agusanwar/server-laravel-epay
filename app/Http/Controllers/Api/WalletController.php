<?php

namespace App\Http\Controllers\Api;

use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function show(){
        // 1. get data user id
        $user = auth()->user();

       // get data wallet in database
       $wallet = Wallet::select(['pin', 'balance', 'card_number'])
                    ->where('user_id', $user->id)
                    ->first();
        
        return response($wallet);
    }

    public function update(Request $request){
        // 1. validasi  data to table user for email exist (test:1 validasi data)
        $validator =  Validator::make($request->all(),[
            'previous_pin' => 'required|digits:6',
            'new_pin' => 'required|digits:6',
       ]);

        // jika validasi fails
        if($validator->fails()){
            return response()->json(['error' => $validator->messages()], 400);
        }
        
        // check pin
        $pinChaker = pinChacker($request->previous_pin);
        // jika pin failed
        if(!$pinChaker){
            return response()->json(['message' => 'Your Old pin is wrong'], 400);
        }

        // get data user id
        $user = auth()->user();

        // prosess update
        Wallet::where('user_id', $user->id)
            ->update(['pin' => $request->new_pin]);

        return response()->json(['mesage' => 'Pin Succcess Update']);
    }
}
