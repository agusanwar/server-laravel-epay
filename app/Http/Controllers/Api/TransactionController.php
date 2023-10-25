<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request){
        // limit paginate
        $limit = $request->query('limit') ? $request->query('limit') : 10;

        // get data user
        $user = auth()->user();

        // rerlaso > 1 table
        $relations = [
            'paymentMethod:id,name,code,thumbnail',
            'transactionType:id,name,code,action,thumbnail',
        ];

        // get transaction db / relasi lebih dari 1 table
        $transactions = Transaction::with($relations)
            ->where('user_id', $user->id)
            ->where('status', 'success')
            ->orderBy('id', 'desc')
            ->paginate($limit);

        // update profile picture to url
        $transactions->getCollection()->transform(function ($item) {
            
            $paymentMethodThumbnail = $item->paymentMethod->thumbnail ?
                url('banks/'.$item->paymentMethod->thumbnail) : "";

            $item->paymentMethod = clone $item->paymentMethod;
            $item->paymentMethod->thumbnail = $paymentMethodThumbnail;

            $transactionType = $item->transactionType;
            $item->thumbnail = $transactionType->thumbnail ?
                url('transaction-type/'.$transactionType->thumbnail) : "";

            return $item;
        });

        return response()->json($transactions);
    }
}
