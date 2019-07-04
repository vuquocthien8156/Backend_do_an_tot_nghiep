<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Services\PermissionService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class PermissionController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(PermissionService $permissionService) {
		$this->permissionService = $permissionService;
	}

	public function PermissionView() {
		$listPermission = $this->permissionService->getListPermission();
		$listUser = $this->permissionService->getListInternalUser();
		return view('phanquyen.phanquyen',['listPermission'=>$listPermission, 'listUser' =>$listUser]);
	}

	public function Permission(Request $request) {
		$id_per = $request->get('id_per');
		$id_user = $request->get('id_user');
		$result = $this->permissionService->Permission($id_per, $id_user);
		if ($result > 0) {
			return response()->json(['status' => 'ok', 'error' => 0]);
		}
		else
		{
			return response()->json(['status' => 'error','error' => 1]);
		}
	}

	public function listPermissionUser() {
		$list = $this->permissionService->listPermissionUser();
		for ($i=0; $i < count($list); $i++) { 
			$getRoll = $this->permissionService->getRoll($list[$i]->tai_khoan);
			$list[$i]->listRoll = $getRoll;
		}
		return response()->json(['status' => 'ok', 'error' => 0, 'list' => $list]);
	}

	public function createPermission(Request $request) {
		$name = $request->get('name');
		$phone = $request->get('phone');
		$email = $request->get('email');
		$password = md5($request->get('password'));
		$permission_group = $request->get('permission_group');
		$inserUser = $this->permissionService->inserUser($name, $phone, $email, $password);
		$getMaxId  = $this->permissionService->getMaxId();
		for ($i=0; $i < count($permission_group); $i++) { 
			$inserPermission = $this->permissionService->inserPermission($getMaxId, $permission_group[$i]);
			if ($inserPermission == true) {
				$t = 1;
			}else {
				$t=0;
			}
		}
		if ($t == 1) {
			return response()->json(['status' => 'ok', 'error' => 0]);
		}else {
			return response()->json(['status' => 'ok', 'error' => 1]);
		}
	}

	public function updatePermission(Request $request) {
		$user_id = $request->get('user_id');
		$permission_group = $request->get('permission_group');
		$deletePermission = $this->permissionService->deletePermission($user_id);
		for ($i=0; $i < count($permission_group); $i++) { 
			$inserPermission = $this->permissionService->inserPermission($user_id, $permission_group[$i]);
			if ($inserPermission == true) {
				$t = 1;
			}else {
				$t=0;
			}
		}
		if ($t == 1) {
			return response()->json(['status' => 'ok', 'error' => 0]);
		}else {
			return response()->json(['status' => 'ok', 'error' => 1]);
		}
	}

	public function deletePermission(Request $request){
		$user_id = $request->get('user_id');
		$deletePermission = $this->permissionService->deletePermission($user_id);
		if ($deletePermission > 0) {
			return response()->json(['status' => 'ok', 'error' => 0]);
		}else {
			return response()->json(['status' => 'ok', 'error' => 1]);
		}
	}
	
}
