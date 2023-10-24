<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'transaction_type_id',
        'payment_method_id',
        'product_id',
        'amount',
        'transaction_code',
        'desc',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    // relasi transaction type
    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }

    // relasi user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi payment method
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // relasi product   
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
