<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulsaPlan extends Model
{
    use HasFactory;
    
    protected $table = 'pulsa_plans';

    protected $fillable = [
        'name',
        'price',
        'vendor_card_id'
    ];
}
