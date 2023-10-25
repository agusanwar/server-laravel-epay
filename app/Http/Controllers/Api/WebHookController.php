<?php

namespace App\Http\Controllers\Api;

use App\Models\Wallet;
use Midtrans\Notification;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class WebHookController extends Controller
{
    public function update(){

        \Midtrans\Config::$serverKey = ('SB-Mid-server-YlbXAIx4PQN8czILCHNyXc60');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
        $notif = new \Midtrans\Notification();

        $transactionStatus = $notif->transaction_status;
        $type = $notif->payment_type;
        $transactionCode = $notif->order_id;
        $fraudStatus = $notif->fraud_status;    

        DB::beginTransaction();
        try {
            // variable status
            $status = null;

            // Sample transactionStatus handling logic
            if ($transactionStatus == 'capture'){
                if ( $fraudStatus == 'challenge'){
                    $status = 'challenge';
                } else if ( $fraudStatus == 'accept'){
                    $status = 'success';
                }

            } else if ($transactionStatus == 'settlement'){
                $status = 'success';

            } else if ($transactionStatus == 'cancel' ||
            $transactionStatus == 'deny' ||
            $transactionStatus == 'expire'){

            $status = 'failed';
            } else if ($transactionStatus == 'pending'){
            $status = 'pending';
            }

            // cek status transaksi
            $transaction = Transaction::where('transaction_code', $transactionCode)->first();
            if($transaction->$status != 'success'){
                $transactionAmount = $transaction->amount;
                $userId = $transaction->user_id;

                $transaction->update(['status' => $status]);

                if($status = 'success'){
                    Wallet::where('user_id', $userId)->increment('balance', $transactionAmount);
                }
            }
            DB::commit();

            return response()->json();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()]);
        }
    }
}
