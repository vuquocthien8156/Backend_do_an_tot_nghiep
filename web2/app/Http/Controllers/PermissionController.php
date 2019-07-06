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

	public function PermissionView(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 3) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$listPermission = $this->permissionService->getListPermission();
		$listUser = $this->permissionService->getListInternalUser();
		return view('phanquyen.phanquyen',['listPermission'=>$listPermission, 'listUser' =>$listUser]);
	}

	public function Permission(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 3) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
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

	public function listPermissionUser(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 3) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$list = $this->permissionService->listPermissionUser();
		for ($i=0; $i < count($list); $i++) { 
			$getRoll = $this->permissionService->getRoll($list[$i]->loai_tai_khoan);
			$getNamePer = $this->permissionService->getNamePer($list[$i]->loai_tai_khoan);
			$list[$i]->listRoll = $getRoll;
			$list[$i]->getNamePer = $getNamePer;
		}
		return response()->json(['status' => 'ok', 'error' => 0, 'list' => $list]);
	}

	public function createPermission(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 3) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$name = $request->get('name');
		$phone = $request->get('phone');
		$email = $request->get('email');
		$password = md5($request->get('password'));
		$permission_group = $request->get('permission_group');
		for ($i=0; $i < count($permission_group); $i++) { 
			$inserUser = $this->permissionService->inserUser($name, $phone, $email, $password, $permission_group[$i]);
			if ($inserUser == true) {
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
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 3) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$user_id = $request->get('user_id');
		$permission_group = $request->get('permission_group');
		for ($i=0; $i < count($permission_group); $i++) { 
			$updatePermission = $this->permissionService->inserPermission($user_id, $permission_group[$i]);
			if ($updatePermission == true) {
				$t = 1;
			}else {
				$t=0;
			}
		}
		if ($t == 1) {
			if ($request->session()->get('user_id') == $user_id) {
				return response()->json(['status' => 'fail', 'error' => 2]);
			}
			return response()->json(['status' => 'ok', 'error' => 0]);
		}else {
			return response()->json(['status' => 'fail', 'error' => 1]);
		}
	}

	public function deletePermission(Request $request){
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 3) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$user_id = $request->get('user_id');
		$status = $request->get('status');
		if ($status == 1) {
			$status = 0;
		}else {
			$status = 1;
		}
		$deletePermission = $this->permissionService->deletePermission($user_id,$status);
		if ($deletePermission > 0) {
			return response()->json(['status' => 'ok', 'error' => 0]);
		}else {
			return response()->json(['status' => 'ok', 'error' => 1]);
		}
	}
	
}
