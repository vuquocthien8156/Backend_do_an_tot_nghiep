<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\ErrorCode;
use App\Enums\EUser;
use App\Helpers\ConfigHelper;
use App\Services\LoginService;
use App\Services\AccountService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class AccountController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(AccountService $accountService) {
		$this->accountService = $accountService;
	}

	public function AccountView() {  
		return view('account.account');
	}

	public function search(Request $request) {
	if ($request->session()->has('name') == false && ($request->session()->has('name') == true && Session::get('vaitro') != 1 && Session::get('type') != 1) && ($request->session()->has('name') == true && Session::get('vaitro') != 1 && Session::get('type') != 2)) {
	 	return redirect('api');
	 } 
		$name = $request->get('name');
		$page = 1;
        if ($request->get('page') !== null) {
            $page = $request->get('page');
        }
        $pathToResource = config('app.resource_url_path');
		$listAccount = $this->accountService->search($name, $page);
		for ($i=0; $i < count($listAccount); $i++) { 
             $listAccount[$i]->pathToResource = $pathToResource;
        }
		return response()->json(['listSearch'=>$listAccount]);
	}

	public function deleteAccount(Request $request) {
	if ($request->session()->has('name') == false && ($request->session()->has('name') == true && Session::get('vaitro') != 1 && Session::get('type') != 1) && ($request->session()->has('name') == true && Session::get('vaitro') != 1 && Session::get('type') != 2)) {
	 	return redirect('api');
	 } 
		$id = $request->get('id');
		$result = $this->accountService->delete($id);
		if ($result != 0) {
			 return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
		}
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
	}

	public function editAccount(Request $request) {
		if ($request->session()->has('name') == false && ($request->session()->has('name') == true && Session::get('vaitro') != 1 && Session::get('type') != 1) && ($request->session()->has('name') == true && Session::get('vaitro') != 1 && Session::get('type') != 2)) {
		 	return redirect('api');
		} 
		$id = $request->get('id');
		$image = $request->get('img');
		$destinationPath = config('app.resource_physical_path');
        $pathToResource = config('app.resource_url_path');
        // $result = $this->accountService->editAccount($id);
        dd($image, $id);
	}
}
