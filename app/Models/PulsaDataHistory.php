<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulsaDataHistory extends Model
{
    use HasFactory;

    protected $table = 'pulsa_plan_histories';

    protected $fillable = [
        'pulsa_plan_id',
        'transaction_id',
        'phone_number'
    ];
}
