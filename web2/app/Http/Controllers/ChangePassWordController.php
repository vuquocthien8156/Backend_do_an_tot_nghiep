<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EDateFormat;
use App\Enums\ELanguage;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Enums\EUserRole;
use App\Enums\ECodePermissionGroup;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Traits\CommonTrait;
use Excel;
use App\Enums\EManufacture;
use App\Services\ChangePassWordService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class ChangePassWordController extends Controller {
	use CommonTrait;

	public function __construct(ChangePassWordService $changePassWordService) {
        $this->changePassWordService = $changePassWordService;
    }

    public function viewchangepassword() {
    	return view('Auth.changepassword');
    }

    public function changepassword(Request $requet) {
    	if (Auth::Check()) {
    		$currentpassword = $requet->get('currentpassword_employees');
    		$name = $requet->get('name_employees');
    		$password = $requet->get('newpassword_employees');
    		$current_password = Auth::User()->password;
    		if (Hash::check($currentpassword, $current_password)) {
    			$user_id = auth()->id();
    			$result = $this->changePassWordService->changepassword($user_id, $password, $name);
    			return redirect()->Route('view-change-password');
    		}
    		 else {
    			$error = array('current-password' => 'Please enter correct current password');
        		return view('Auth.changepassword',['error' => $error]);
    		}	
    	} else {
    		return redirect()->to('/');
    	}
    }
}