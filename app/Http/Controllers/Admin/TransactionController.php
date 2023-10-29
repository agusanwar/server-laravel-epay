<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index()
    {
        $relations = [
            'paymentMethod:id,name,code,thumbnail',
            'user:id,name',
            'transactionType:id,code,action',

        ];
        $transactions = Transaction::with($relations)
                                ->orderBY('created_at', 'desc')->get();

        return view('transaction', ['transactions' => $transactions]);
    }
}
