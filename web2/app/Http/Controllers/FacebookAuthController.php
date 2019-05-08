<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Socialite;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Enums\EUserRole;
use App\Helpers\ConfigHelper;
use App\Services\LoginService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class FacebookAuthController extends Controller
{
    public function __construct(LoginService $loginService) {
        $this->loginService = $loginService;
    }

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
        return redirect()->route('home',['status' => 'Thành công', 'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'avatar'=> $user->avatar]);
    }
    
    private function findOrCreateUser($facebookUser){
        $authUser = User::where([
            'fb_id' => $facebookUser->id,
            'email' => $facebookUser->email
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

    public function insertNoMail(Request $request) {
        $id_fb = $request->get('id_fb');
        return User::create([
            'fb_id' => $id_fb,
        ]);
    }

    public function loginfb(Request $request) {
        $id_fb = $request->get('id_fb');
        $email = $request->get('email');
        $type = $request->get('type');
        if ($type == 1) {
            if ($request->session()->has('name') == true) {
                return redirect('home');
            }
            $user = Socialite::driver('facebook')->user();
            $authUser = $this->login($user);
            // Chỗ này để check xem nó có chạy hay không
            session()->put('name',$user->email);
            session()->put('login',true);
            Auth::login($authUser, true);
            return redirect()->route('home',['status' => 'Thành công', 'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'avatar'=> $user->avatar]);
        }
        if ($type == 2) {
            $id_fb = $request->get('id_fb');
            return User::create([
                'fb_id' => $id_fb,
            ]);
        }
        if ($type == 3) {
            if ($request->session()->has('name') == true) {
                return redirect('home');
            }
            $user = Socialite::driver('facebook')->user();
            $authUser = $this->firsLogin($user);
            // Chỗ này để check xem nó có chạy hay không
            session()->put('name',$user->email);
            session()->put('login',true);
            Auth::login($authUser, true);
            return redirect()->route('home',['status' => 'Thành công', 'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'avatar'=> $user->avatar]);
        }
        if($type == 4) {
            $updateIdFB =  $this->loginService->updateIdFB($id_fb, $email);
            $getInfo = $this->loginService->getInfo($id_fb);
            if (isset($getInfo[0]->ten)) {
                return response()->json(['status' => 'ok', 'error' => 0, 'infoUser' => $getInfo]);
            }
            return response()->json(['status' => 'fail', 'error' => 1]);
        }
        if ($type == 5) {
            $updateIdFB =  $this->loginService->updateIdFB($id_fb, $email);
            $insertPass = $this->loginService->insertPass($id_fb);
            if (isset($getInfo[0]->ten)) {
                return response()->json(['status' => 'ok', 'error' => 0, 'infoUser' => $getInfo]);
            }
            return response()->json(['status' => 'fail', 'error' => 1]);
        }
    }

    private function login($facebookUser){
        $authUser = User::where([
            'fb_id' => $facebookUser->id,
            'email' => $facebookUser->email
        ])->first();
 
        if($authUser){
            return $authUser;
        }
    }

    private function firsLogin($facebookUser){
        return User::create([
            'ten' => $facebookUser->name,
            'email' => $facebookUser->email,
            'fb_id' => $facebookUser->id,
        ]);
    }
}
