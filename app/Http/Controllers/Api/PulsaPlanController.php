<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PulsaPlan;
use App\Models\PulsaPlanHistory;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PulsaPlanController extends Controller
{
    public function store(Request $request){
        // 1. validasi add data to table pulsa plan (test:1 validasi data)
        $validator = Validator::make($request->all(), [
            'pulsa_plan_id' => 'required|integer',
            'phone_number' => 'required|string',
            'pin' => 'required|digits:6'
        ]);

        // jika validasi fails
        if($validator->fails()){
            return response()->json(['error' => $validator->messages()], 400);
        }

        // get data user id
        $userId = auth()->user()->id;

        // get data transaction type
        $transactionType = TransactionType::where('code', 'pulsa')->first();

        // get data payment method
        $paymentMethod = PaymentMethod::where('code', ['bri_va', 'bca_va', 'bni_va'])->first();
 
        // data wallet user in top up
        $userWallet = Wallet::where('user_id', $userId)->first();
 
        // check data paln id in database  (test:1 check data)
        $dataPlan = PulsaPlan::find($request->pulsa_plan_id);
            // jika tidak ada data
            if(!$dataPlan){
                return response()->json(['message' => 'Pulsa Plan not found'], 404);
            }
 
        // check pin
        $pinChaker = pinChacker($request->pin);
            // jika pin failed
            if(!$pinChaker){
                return response()->json(['message' => 'PIN is Wrong'], 400);
            }
        
        // check balance good or not
        if($userWallet->balance < $dataPlan->price){
            return response()->json(['message' => 'Your balance is not enought'], 400);
        }

         // 2. insert to table transaction (test:2 creata & save data to database)
         DB::beginTransaction();

         try {
             // save data to tb transaction
            $transaction = Transaction::create([
             'user_id' => $userId,
             'transaction_type_id' => $transactionType->id,
             'payment_method_id' => $paymentMethod->id,
             'amount' => $dataPlan->price,
             'transaction_code' => strtoupper(Str::random(10)),
             'desc' =>'Pulsa Plan Provider'.$dataPlan->name,
             'status' => 'succes',
            ]);
         
             // save data to tb plan history
             PulsaPlanHistory::create([
                 'pulsa_plan_id' => $request->pulsa_plan_id,
                 'transaction_id' => $transaction->id,
                 'phone_number' => $request->phone_number,
            ]);
 
            // decrement from balance to min price (decrement)
            $userWallet->decrement('balance', $dataPlan->price);
 
            // save DB (test:2 save data to database)
            DB::commit();
 
            return response()->json(['message' => 'By Pulsa Plan success']);
         } catch (\Throwable $th) {
             // 2. jika prosses data error (step:2 save data to database failed)
             DB::rollBack();
 
             return response()->json(['message', $th->getMessage()], 500);
         }
    }
}
