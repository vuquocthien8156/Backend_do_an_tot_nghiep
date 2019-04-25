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
		$listPermission = $this->permissionService->getListpermission();
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
	
}
