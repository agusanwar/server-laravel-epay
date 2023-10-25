<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCard extends Model
{
    use HasFactory;

    protected $table = 'vendor_cards';

    protected $fillable = [
        'name',
        'thumbnail',
        'status',
    ];

    // relasi one vendor cart to many data plan 
    public function dataPlans(){
        return $this->hasMany(DataPlan::class);
    }

    // relasi one vendor cart to many pulsa plan 
    public function pulsaPlans(){
        return $this->hasMany(PulsaPlan::class);
    }
}
