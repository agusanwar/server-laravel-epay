<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHistory extends Model
{
    use HasFactory;

    protected $table = 'transfer_historys';

    protected $fillable = [
        'seeder_id',
        'receiver_id',
        'transaction_code',
    ];

    // format timestamp
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function receiverUser()
    {   
        // relasi to user, Who has this id
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}
