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

    // relasi data
    public function dataPlans(){
        return $this->hasMany(DataPlan::class);
    }

    
    // relasi pulsa
    public function pulsaPlans(){
        return $this->hasMany(PulsaPlan::class);
    }


}
