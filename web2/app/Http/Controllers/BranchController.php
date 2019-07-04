<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Enums\ECodePermissionGroup;
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

	public function branchView(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$name = $request->get('name');
		$place = $request->get('place');
		$listPlace = $this->branchService->listPlace();
		$listBranch = $this->branchService->listBranch($name, $place);
    	return view('branch.branch', ['list' => $listPlace, 'listBranch' => $listBranch]);
    }

    public function searchBranch(Request $request) {
    	$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		if ($request->get('page') === null) {
            $page = $request->get('page');
        }else {
        	$page = 1;
        }
        $pathToResource = config('app.resource_url_path');
        $listBranch = $this->branchService->listBranch($page);
		return response()->json(['listBranch'=>$listBranch]);  
	}

	public function saveBranch(Request $request){
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$name = $request->get('name_branch');
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
		$phone_branch = $request->get('phone_branch');
		$address = $request->get('address');
		$id_kv = $request->get('id_kv');
		$saveBranch = $this->branchService->saveBranch($name, $latitude, $longitude, $phone_branch, $address, $id_kv);
		if ($saveBranch == true) {
			return response()->json(['status' => 'error', 'error' => 0]);
		}else {
			return response()->json(['status' => 'error', 'error' => 1]);
		}
	}

	public function deleteBranch(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$id = $request->get('id_branch');
		$deleteBranch = $this->branchService->deleteBranch($id);
		if ($deleteBranch == 1) {
			return response()->json(['status' => 'error', 'error' => 0]);
		}else {
			return response()->json(['status' => 'error', 'error' => 1]);
		}
	}

	public function updateBranch(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$id_branch_update = $request->get('id_branch_update');
		$address_update = $request->get('address_update');
		$phone_branch_update = $request->get('phone_branch_update');
		$name_branch_update = $request->get('name_branch_update');
		$longitude_update = $request->get('longitude_update');
		$latitude_update = $request->get('latitude_update');
		$id_kv = $request->get('id_kv');
		$updateBranch = $this->branchService->updateBranch($id_branch_update, $address_update, $phone_branch_update, $name_branch_update, $latitude_update, $longitude_update, $id_kv);
		if ($updateBranch == 1) {
			return response()->json(['status' => 'error', 'error' => 0]);
		}else {
			return response()->json(['status' => 'error', 'error' => 1]);
		}
	}
}
