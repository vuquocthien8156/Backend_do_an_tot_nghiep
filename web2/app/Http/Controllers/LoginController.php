<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EDateFormat;
use App\Enums\ELanguage;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EVehicleStatus;
use App\Enums\EVehicleType;
use App\Enums\ErrorCode;
use App\Enums\EUserRole;
use App\Enums\EAppointmentType;
use App\Enums\EVehicleAccredited;
use App\Enums\EManufacture;
use App\Enums\ECodePermissionGroup;
use App\Enums\EVehicleDisplayOrder;
use App\Helpers\ConfigHelper;
use App\Services\LoginService;
use App\Services\VehicleService;
use App\Services\ConfigService;
use App\Traits\CommonTrait;
use App\Exports\VehicleExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class LoginController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(LoginService $loginService) {
		$this->loginService = $loginService;
	}

	public function loginView(Request $request) {
		if ($request->session()->has('name') == false) {
	  		return view('login.login2');
	  	}
	  	else {
	  		return redirect()->route('home');
	  	}  
	}
	
	public function check(Request $request) {
		$user = $request->get("username");
		$pass = $request->get("password");
		$check = $this->loginService->check($user);
		if (isset($check[0]->email)) {
			return response()->json(['status' => 'ok', 'error' => 0, $check]);
		}
		else
		{
			return response()->json(['status' => 'error', 'error' => 1, 'message' => 'account is not exist']);
		}
	}

	public function login(Request $request) {
	if ($request->session()->has('name') == true) {
	  		return redirect()->route('home');
	  	}  
		$user = $request->get("username");
		$pass = $request->get("password");
		$check = $this->loginService->login($user, $pass);
		if (isset($check[0]->user_id)) {
			session()->put('id',$check[0]->user_id);
			session()->put('name',$check[0]->ten);
			session()->put('login',true);
			session()->put('vaitro',$check[0]->vaitro);
			session()->put('type',$check[0]->type);
			return response()->json(['status' => 'ok', 'error' => 0, $check]);
		}
		else
		{
			return response()->json(['status' => 'error','error' => 1]);
		}
	}

	public function loginsdt(Request $request) {
	if ($request->session()->has('name') == true) {
	  		return redirect()->route('home');
	  	}  
		$user = $request->get("username");
		$check = $this->loginService->loginsdt($user);
		if (isset($check[0]->user_id)) {
			session()->put('id',$check[0]->user_id);
			session()->put('name',$check[0]->ten);
			session()->put('login',true);
			session()->put('vaitro',$check[0]->vaitro);
			session()->put('type',$check[0]->type);
			return response()->json(['status' => 'ok', 'error' => 0, $check]);
		}
		else
		{
			return response()->json(['status' => 'error','error' => 1]);
		}
	}

	public function logout(Request $request) {  
		session()->flush();
		return redirect('api');
	}
	
}
