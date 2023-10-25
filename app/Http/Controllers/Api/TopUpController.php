<?php

namespace App\Http\Controllers\Api;


use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\TransactionType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class TopUpController extends Controller
{   
    // store data
    public function store(Request $request){
        // 1. get request data in body
        $data = $request->only('amount', 'pin', 'payment_method_code');
        
        // validasi data 
        $validator = Validator::make($data, [ 
            'amount' => 'required|integer|min:10000',
            'pin' => 'required|digits:6',
            'payment_method_code' => 'required|in:bni_va,bca_va,bri_va'
        ]);
        
        // cek validasi data (test:1 validasi data)
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        // check request data pin
        $pinChaker = pinChacker($request->pin);

        if(!$pinChaker){
            return response()->json(['message' => 'PIN is Wrong'], 400);
        }

        // 2. insert data to database tb transaction (test:2 to database)
        $transactionType = TransactionType::where('code', 'top_up')->first();
        $paymentMethod = PaymentMethod::where('code', $request->payment_method_code)->first();

        // database transaction
        DB::beginTransaction();

        try {
           // 2. create to table transaction
           $transaction = Transaction::create([
                'user_id' => auth()->user()->id,
                'transaction_type_id' => $transactionType->id,
                'payment_method_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'transaction_code' => strtoupper(Str::random(10)),
                'desc' => 'Top Up via '.$paymentMethod->name,
                'status' => 'pending',
            ]);
            
            //  3.1 call midtrans array
            $params =  $this->buildMidtransParams([
                'transaction_code' => $transaction->transaction_code,
                'amount' => $transaction->amount,
                'payment_method' => $paymentMethod->code,
            ]);

            // 3.1 call midtrans
            $midtrans = $this->callMidtrans($params);

            // 2. save to database
            DB::commit();
            
            // 3. response midtrans
            return response()->json($midtrans);
        } catch (\Throwable $th) {
            // 2. jika prosses data error
            DB::rollback();
    
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

     // 3. call -> integrasi midtrans 
    private function callMidtrans(array $params)
    {
        // confogurate midtrans
        \Midtrans\Config::$serverKey = ('SB-Mid-server-YlbXAIx4PQN8czILCHNyXc60');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false); // true
        \Midtrans\Config::$isSanitized = (bool) env('MIDTRANS_IS_SANITIZED'); // true
        \Midtrans\Config::$is3ds = (bool) env('MIDTRANS_IS_3DS'); //false


        // create transaction to midtarns
        $createTransaction = \Midtrans\Snap::createTransaction($params);

        // return array
        return [
            'redirect_url' => $createTransaction->redirect_url,
            'token' => $createTransaction->token,
        ];
    }

    // 3.2 build params array params
    private function buildMidtransParams(array $params){
        $transactionDetails = [
            'order_id' => $params['transaction_code'],
            'gross_amount' => $params['amount'],
        ];

        $user = auth()->user();
        $splitName = $this->splitName($user->name);
        $customerDetail =   array(
            'first_name' => $splitName['first_name'],
            'last_name' => $splitName['last_name'],
            'email' => $user->email
        );
        
        // tipe payment midtrans in use for midtrans
        $enabledPayment = [
            $params['payment_method'],
        ];

        // return build parameter
        return [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetail,
            'enabled_payments' => $enabledPayment,
        ];
    }

    // 3.3 midtrans membutuhkan 2 kolom / row cara create spleat kolom
    private function splitName($fullName){
        $name = explode(' ', $fullName);
        // case name
        // anita cintia putri
        //['anita', 'cintia', 'putri']

        // array pop for cut array in fullname
        $lastName = count($name) > 1 ? array_pop($name) : $fullName;
        $firstName = implode(' ', $name);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
    }
}
