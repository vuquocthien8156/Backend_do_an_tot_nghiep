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
use App\Services\PermissionService;
use App\Services\ConfigService;
use App\Traits\CommonTrait;
use App\Exports\VehicleExport;
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

	
}
