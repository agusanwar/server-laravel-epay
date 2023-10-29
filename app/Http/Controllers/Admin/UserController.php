<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select(
            'id', 'name','email', 'username', 'verified', 'profile_picture', 'identitas', 'created_at' )
            ->where('username', 'like', '%'.'%')
            ->get();

        return view('user', ['users' => $users]);
    }
}
