<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Enums\ErrorCode;
use Laravel\Socialite\Facades\Socialite;
use User;
class AuthenticateController extends Controller {
    
    public function authenticate(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json(['message' => 'Unauthorized'], ErrorCode::UNAUTHORIZED);
        $user = $request->user();
        return response()->json(['message' => 'Success!']);
    }    
}
