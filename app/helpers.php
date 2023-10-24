<?php

use App\Models\User;
use App\Models\Wallet;

// get user helpers
function getUser($param){
    // get user
    $user = User::where('id', $param)
                ->orWhere('email', $param)
                ->orWhere('username', $param)
                ->first();
    // set wallet     
    $wallet = Wallet::where('user_id', $user->id)->first();
    // check picture
    $user->profile_picture = $user->profile_picture ? url('storage/'.$user->profile_picture) : "";
    $user->identitas = $user->identitas ? url('storage/'.$user->identitas) : "";
    $user->balance = $wallet->balance;
    $user->card_number = $wallet->card_number;
    $user->pin = $wallet->pin;

    return $user;
}

// pin helpers
function pinChacker($pin){
    $userId = auth()->user()->id;
    $wallet = Wallet::where('user_id', $userId)->first();

    // check user wallet
    if(!$wallet) {
        return false;
    }
    if($wallet->pin == $pin) {
        return true;
    }
    return false;
}
