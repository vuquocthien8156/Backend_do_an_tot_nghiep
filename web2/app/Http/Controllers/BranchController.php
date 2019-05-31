<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Helpers\ConfigHelper;
use App\Services\BranchService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Hash;
use Excel;

class BranchController extends Controller {
	use CommonTrait;

	public function __construct(BranchService $branchService) {
		$this->branchService = $branchService;
	}

	public function branchView() {
		$list = $this->branchService->listPlace();
    	return view('branch.branch', ['list' => $list]);
    }

    public function searchBranch(Request $request) {
		$name = $request->get('name');
		$place = $request->get('place');
        $pathToResource = config('app.resource_url_path');
        $listBranch = $this->branchService->listBranch($name, $place);
		return response()->json(['listBranch'=>$listBranch]);  
	}
}
