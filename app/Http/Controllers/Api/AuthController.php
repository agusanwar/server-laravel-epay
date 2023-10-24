<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;

class AuthController extends Controller
{
    // Register
    public function register(Request $request){
        // 1. request  data 
        $data = $request->all();

        // 2. validasi data
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'pin' => 'required|digits:6',
        ]);

        // 3. cek validasi data (test:1)
        if($validator->fails()){
            return response()->json(['error' => $validator->messages()], 400);
        }

        // 4. cek data email
        $user = User::where('email', $request->email)->exists();
        if($user){
            return response()->json(['message' => 'Email Alredy Token'], 409);
        }

        // 9. check jika salah 1 post data to database error 
        DB::beginTransaction();
        
        try {
            // 5. upload image (test:2)
            $profilePicture = null;
            $identitas = null;

            if($request->profile_picture){
                $profilePicture = $this->uploadBase64Image($request->profile_picture);
            }
            if($request->identitas){
                $identitas = $this->uploadBase64Image($request->identitas);
            }

            // 6. register user (test:3 to dataabase)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->email,
                'password' => bcrypt($request->password),
                'profile_picture' => $profilePicture,
                'identitas' => $identitas,
                'verified' => ($identitas) ? true : false,
            ]);

            // 7. wallet (test:3 to database & first user register)
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'pin' => $request->pin,
                'card_number' => $this->generateCardNumber(16),
            ]);

            // 10. jika success add data to database
            DB::commit();

            // 12. register to automatic login & get token
            $token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password ]);

            // 13. gett data user
            $userResponse = getUser($request->email);
            $userResponse->token = $token;
            $userResponse->token_expires_in = auth()->factory()->getTTL() * 60;
            $userResponse->token_type = 'bearer';

            return response()->json($userResponse);
        } catch (\Throwable $th) {
            throw $th;
            // 11. jika prosses data error
            DB::rollback();
            
            // 8. if check error (proses test 3)
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    // API LOGIN
    public function login(Request $request)
    {   
        // 1. request only email and pass
        $credentials = $request->only('email', 'password');

        // 2. validasi data
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // 3. validasi jika error
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            // 4. generate token success (test: 1)
            $token = JWTAuth::attempt($credentials);

            // 5. token salah
            if (!$token) {
                return response()->json(['message' => 'Login credentials are invalid'], 400);
            }

            // 7. get data user in halpers
            $userResponse = getUser($request->email);

            // 8. set token
            $userResponse->token = $token;

            // 9. set token expired
            $userResponse->token_expires_in = auth()->factory()->getTTL() * 60;
            $userResponse->token_type = 'bearer';

            return response()->json($userResponse);
            // return $token;
        } catch (JWTException $e) {
            // 6. step 1. error
             return response()->json(['message' => $e->getMessage(), 500]);
        }
       
      
    }

    // 5. upload image base 64
    private function uploadBase64Image($base64Image)
    {
        $decoder = new Base64ImageDecoder($base64Image, $allowedFormats = ['jpeg','png','jpg']);

        $decodedContent = $decoder->getDecodedContent();
        $format = $decoder->getFormat();
        $image = Str::random(10).'.'.$format;
        
        //save storage
        Storage::disk('public')->put($image, $decodedContent);

        return $image;
    }

    // 7. in register card number generate
    private function generateCardNumber($length)
    {
        $result = '';
        for ($i=0; $i < $length; $i++) { 
            $result .= mt_rand(0, 9);
        }

        // cek generate data in database
        $wallet = Wallet::where('card_number', $result)->exists();

        if($wallet){
            return $this->generateCardNumber($length);
        }

        return $result;
    }
}
