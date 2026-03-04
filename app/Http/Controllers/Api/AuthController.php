<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function login(request $request)
    {
       $request->validate([
        'email' => 'required|email',
        'password' => 'required'
       ]);
       if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'message' => 'Invalid login details'
        ], 401);
       }

       $user = Auth::user();
       $token = $user->createToken('react-app')->plainTextToken;

         return response()->json([
          'user' => $user,
          'token' => $token
         ]);
    }
}
