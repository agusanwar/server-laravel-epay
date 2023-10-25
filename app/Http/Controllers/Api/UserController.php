<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show(){
        // 1. get data user (test:1)
        $user = getUser(auth()->user()->id);

        return response()->json($user);
    }

    public function getUserByUsername(Request $request, $username){
        // select data user
        $user = User::select(
                        'id', 'name', 'username', 'verified', 'profile_picture' )
                        ->where('username', 'like', '%'.$username.'%')
                        ->where('id', '<>', auth()->user()->id) // <> for id user not redudance
                        ->get();
        $user->map(function($item){
            $item->profile_picture = $item->profile_picture ?
                url('storage/'.$item->profile_picture) : "";

            return $item;
        });           
        return response()->json($user);    
    }

    public function update(Request $request){
        try {
            // 2. get data user (test:2 get data user by id)
            $user = User::find(auth()->user()->id);

            // ger request data
            $data = $request->only('name', 'username', 'email', 'password', 'identitas');

            // check duplicate data user
            if($request->username != $user->username){
                // check data username
                $isExistUsername = User::where('username', $request->username)->exists();
                if($isExistUsername){
                    return response(['message' => 'username alredy taken'], 400);
                }
            }

            // 3. check data email duplicate (test:3 validasi data duplicate)
            if($request->email != $user->email){
                // check data email
                $isExistEmail = User::where('email', $request->email)->exists();
                if($isExistEmail){
                    return response(['message' => 'email alredy taken'], 400);
                }
            }

            // 4. check data send password
            if($request->password){
                // check data username
                $data['password'] = bcrypt($request->password);
            }

            // 5.check kondition upload or reflace profile picture
            if($request->profile_picture){
                $profile_picture = uploadBase64Image($request->profile_picture);
                $data['profile_picture'] = $profile_picture;
                // profile picture in database
                if($user->profile_picture){
                    Storage::delete('public', $user->profile_picture);
                }
            }

            // 6. check kondition upload or reflace identitas
            if($request->identitas){
                $identitas = uploadBase64Image($request->identitas);
                $data['identitas'] = $identitas;
                $data['verified'] = true;
                // profile picture in database
                if($user->identitas){
                    Storage::delete('public', $user->identitas);
                }
            }

            // uptade to database

            $user->update($data);
            return response()->json(['message' => 'User Updated']);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function isEmailExists(Request $request){
       // 1. validasi  data to table user for email exist (test:1 validasi data)
       $validator =  Validator::make($request->only('email'),[
            'email' => 'required|email'
       ]);

        // jika validasi fails
        if($validator->fails()){
            return response()->json(['error' => $validator->messages()], 400);
        }

        // if valdasi succes not error
        $isExist = User::where('email', $request->email)->exists();

        // response
        return response()->json(['is_email_exists' => $isExist]);

    }
}
