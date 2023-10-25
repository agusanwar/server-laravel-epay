<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VendorCard;
use Illuminate\Http\Request;

class VendorCardController extends Controller
{
    public function index(Request $request){
        // 2. set query params limit (test:1 set limit )
        $limit = $request->query('limit') ? $request->query('limit') : 10;

        // 1. get data object, query params (test:1 get data )
        $vendorCard = VendorCard::with('dataPlans', 'pulsaPlans')
                ->where('status', 'active')
                ->paginate($limit);
        
        // get thumbnail vendor cards
        $vendorCard->getCollection()->transform(function ($item) {
            $item->thumbnail = $item->thumbnail ? url($item->thumbnail) : "";
            return $item;
        });

        return response()->json($vendorCard);
    }
}
