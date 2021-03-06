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
        $name = $request->get('name');
        $avatar = $request->get('avatar');

        $checkIdFB = $this->loginService->check($id_fb);
        $checkEmail =  $this->loginService->check($email);

        // 'type' => 3
        if (!isset($checkIdFB[0]->fb_id) && !isset($checkEmail[0]->email)) {
            $create = $this->loginService->create($id_fb, $email , $name , $avatar);
            if ($create == 1) {
               $idMax = $this->loginService->idMax();
               return response()->json(['status' => 'ok', 'error' => 0  , 'info' => ['user_id' => $idMax , 'ten'=>$name , 'email'=> $email , 'avatar' => $avatar , 'fb_id' => $id_fb ] ]);
            }
            return response()->json(['status' => 'fail', 'error' => 1]);
        }

        // 'type' => 4
        if(!isset($checkIdFB[0]->fb_id) && isset($checkEmail[0]->email) && !isset($checkEmail[0]->fb_id)) {
            $updateIdFB =  $this->loginService->updateUserFB($id_fb, $email , 4);
            $getInfo = $this->loginService->getInfo($id_fb);
            if (isset($getInfo[0]->email)) {
                return response()->json(['status' => 'ok', 'error' => 0, 'info' => $getInfo[0]]);
            }
            return response()->json(['status' => 'fail', 'error' => 1]);
        }

        // 'type' => 2
        if (!isset($checkIdFB[0]->fb_id) && isset($checkEmail[0]->email)) {
            $email = null;
            $create = $this->loginService->create($id_fb, $email , $name , $avatar);

            if ($create == 1) {
                $idMax = $this->loginService->idMax();
               return response()->json( ['status' => 'ok',
                     'error' => 0 , 
                     'info' => ['user_id' => $idMax , 'ten'=>$name , 'avatar' => $avatar , 'fb_id' => $id_fb ] ]);
            }
            return response()->json(['status' => 'fail', 'error' => 1]);
        }

        //  'type' => 5
        if (!isset($checkIdFB[0]->email) && !isset($checkEmail[0]->email) && isset($checkIdFB[0]->fb_id) ) {
            $updateIdFB =  $this->loginService->updateUserFB($id_fb, $email , 5);
            $getInfo = $this->loginService->getInfo($id_fb);
            if (isset($getInfo[0]->email)) {
                return response()->json(['status' => 'ok', 'error' => 0, 'info' => $getInfo[0] ]);
            }
            return response()->json(['status' => 'fail', 'error' => 1]);
        }

        //  'type' => 1
        if (isset($checkIdFB[0]->fb_id)) {
            $login = $this->loginService->loginfb($id_fb);
            if (isset($login[0]->fb_id)) {
               return response()->json(['status' => 'ok', 'error' => 0,'info' => $login[0] ]);
            }
            return response()->json(['status' => 'fail', 'error' => 1]);
        }
    }
}
