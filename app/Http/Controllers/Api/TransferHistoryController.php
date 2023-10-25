<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TransferHistory;
use App\Http\Controllers\Controller;

class TransferHistoryController extends Controller
{
    public function index(Request $request){
        // limit paginate
        $limit = $request->query('limit') ? $request->query('limit') : 10;

        //1. get user sender
        $sender = auth()->user();

        // get datain database
        $transferHistory = TransferHistory::with('receiverUser:id,name,username,verified,profile_picture')
                ->select('receiver_id')
                ->where('seeder_id', $sender->id)
                ->groupBy('receiver_id')
                ->paginate($limit);
                
        // update profile picture to url
        $transferHistory->getCollection()->transform(function ($item) {
            $receiverUser = $item->receiverUser;
            $item->profile_picture = $receiverUser->profile_picture ? url('storage/'.$receiverUser->profile_picture) : "";
            return $item;
        });

        return response()->json($transferHistory);
    }
}
