<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use Socialite;
use Illuminate\Support\Facades\Hash;

class FacebookAuthController extends Controller
{
     public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }
 
    /**
     * Obtain the user information from facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        if ($request->session()->has('name') == true) {
            return redirect('home');
        }
        $user = Socialite::driver('facebook')->user();
        $authUser = $this->findOrCreateUser($user);
        // Chỗ này để check xem nó có chạy hay không
 		session()->put('name',$user->email);
		session()->put('login',true);
        Auth::login($authUser, true);
        return redirect()->route('home',['status' => 'Thành công']);
    }
 
    private function findOrCreateUser($facebookUser){
        $authUser = User::where([
            'fb_id' => $facebookUser->id
        ])->first();
 
        if($authUser){
            return $authUser;
        }
 
        return User::create([
            'ten' => $facebookUser->name,
            'email' => $facebookUser->email,
            'fb_id' => $facebookUser->id,
        ]);
    }
}
