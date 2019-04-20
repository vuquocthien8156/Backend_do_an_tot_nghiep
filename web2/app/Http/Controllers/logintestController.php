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
use App\Services\CustomerService;
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

class logintestController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(ConfigService $configService, VehicleService $vehicleService) {
		$this->configService = $configService;
		$this->vehicleService = $vehicleService;
	}

	public function d() {  
		return view('test.test');
	}

	public function index() {  
		return view('auth.logins');
	}
}
