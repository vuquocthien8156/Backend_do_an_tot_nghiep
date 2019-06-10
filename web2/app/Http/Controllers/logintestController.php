<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Helpers\ConfigHelper;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class logintestController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function d() {  
		return view('productdetail.detail');
	}

	public function index() {  
		return view('auth.logins');
	}
}
