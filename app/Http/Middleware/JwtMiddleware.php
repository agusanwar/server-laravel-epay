<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // cek valid token
            $user = JWTAuth::parseToken()->authenticate();
 
         } catch (JWTException $e) {
             if($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                 return response()->json(['message'=> 'Token invalid'], 401);
             }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                 return response()->json(['message'=> 'Token Expired'], 401);
             }else {
                 return response()->json(['message'=> 'Authorization code not found'], 401);
             }
         }
         return $next($request);
    }
}
