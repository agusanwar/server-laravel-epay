<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\TransactionType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TransferHistory;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
    public function store(Request $request){
        // 1. get request data in body
        $data = $request->only('amount', 'pin', 'send_to');

        // validasi data 
        $validator = Validator::make($data, [
            'amount' => 'required|integer|min:10000',
            'pin' => 'required|digits:6',
            'send_to' => 'required',
        ]);

        // cek validasi data (test:1 validasi data)
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        // data user penerima (test:1 get data)
        $sender = auth()->user();
        // get data user 
        $receiver = User::select('users.id', 'users.username')
                    ->join('wallets', 'wallets.user_id', 'users.id')
                    ->where('users.username', $request->send_to)
                    ->orWhere('wallets.card_number', $request->send_to)
                    ->first();

        // check request data pin
        $pinChaker = pinChacker($request->pin);

        if(!$pinChaker){
            return response()->json(['message' => 'PIN is Wrong'], 400);
        }

        // check penerima
        if(!$receiver){
            return response()->json(['message' => 'User receiver Not Found'], 404);
        }

        //check for transaction to personal / call back
        if($sender->id == $receiver->id){
            return response()->json(['message' => 'You can not transafer for your self'], 400);
        }

        // get data sender wallet
        $senderWallet = Wallet::where('user_id', $sender->id)->first();

        // Jika balance limit or not balance found
        if($senderWallet->balance < $request->amount){
            return response()->json(['message' => 'You Balance is not enough'], 400);
        }

        // 2. Transfer to mamber e pay (test:2 transfer data to user e pay)
        DB::beginTransaction();

        try {
            // get data transaction type
            $transactionType = TransactionType::whereIn('code', ['receive', 'transfer'])
                                        ->orderBy('code', 'asc')
                                        ->get();

            // get data only in transaction type
            $receiveTransactionType = $transactionType->first();
            $transferTransactionType = $transactionType->last();

                $transactionCode =  strtoupper(Str::random(10));
                $paymentMethod = PaymentMethod::where('code', ['bri_va', 'bca_va', 'bni_va'])->first();

            // create transaction for transfer
            $transferTransaction = Transaction::create([
                'user_id' => $sender->id,
                'transaction_type_id' => $transferTransactionType->id,
                'desc' => 'Transfer to '.$receiver->username,
                'amount' => $request->amount,
                'transaction_code' => $transactionCode,
                'status' => 'success',
                'payment_method_id' => $paymentMethod->id,
            ]);

             // prosses min balance for transfer user
            $senderWallet->decrement('balance', $request->amount);

            // create transaction for receiver
            $receiveTransaction = Transaction::create([
                'user_id' => $receiver->id,
                'transaction_type_id' => $receiveTransactionType->id,
                'desc' => 'Reeciver from Sender '.$sender->username,
                'amount' => $request->amount,
                'transaction_code' => $transactionCode,
                'status' => 'success',
                'payment_method_id' => $paymentMethod->id,
            ]);

            // prosses add balance in transfer user
            Wallet::where('user_id', $receiver->id)->increment('balance', $request->amount);

            // create data in transaction history
            TransferHistory::create([
                'seeder_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'transaction_code' => $transactionCode
            ]);

            // 2. save to database (step:2 save data to database)
            DB::commit();
            return response(['message' => 'Transfer Success']);
          
        } catch (\Throwable $th) {
            // 2. jika prosses data error (step:2 save data to database failed)
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
