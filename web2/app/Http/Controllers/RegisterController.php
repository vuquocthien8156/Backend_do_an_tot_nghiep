<?php

namespace App\Http\Controllers;

use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Helpers\ConfigHelper;
use App\Traits\CommonTrait;
use App\Exports\VehicleExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class RegisterController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(RegisterService $registerService) {
		$this->registerService = $registerService;
	}

	public function registerView() {  
		return view('register.register');
	}
	public function register(Request $request) {  
		$username = $request->get("username");
		$password = $request->get("password");
		$name = $request->get("name");
		$gender = $request->get("gender");
		$birthday = $request->get("birthday");
		$phone = $request->get("phone");
		$address = $request->get("address");
		$check = $this->registerService->getAccount();
		for ($i=0; $i < count($check); $i++) { 
			if ($username == $check[$i]->email) {
				return \Response::json(['status' =>"already",'success' => false]);
			}
		}
		$insert = $this->registerService->insertUser($username, $password, $name, $gender, $birthday, $phone, $address);
		$idMax = $this->registerService->idMax();
		$Permission = $this->registerService->insertPermission($idMax);
		if ($Permission == true) {
			return \Response::json(['status' =>"ok",'success' => true, 'error' => 0]);
		}
		return \Response::json(['status' =>"error",'success' => false, 'error' => 1]);
	}
}
