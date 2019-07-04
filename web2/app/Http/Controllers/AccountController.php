<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\ErrorCode;
use App\Enums\EUser;
use App\Enums\EDateFormat;
use App\Helpers\ConfigHelper;
use App\Services\LoginService;
use App\Services\AccountService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use App\Exports\AccountExport;
use Illuminate\Support\Facades\Gate;
use Excel;

class AccountController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(AccountService $accountService) {
		$this->accountService = $accountService;
	}

	public function AccountView(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 2) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		return view('account.account');
	}

	public function search(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 2) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$name = $request->get('name');
		$phone = $request->get('phone');
		$gender = $request->get('gender');
		$infoExportExcel = ['name' => $name, 'phone' => $phone, 'gender' => $gender];
		$page = 1;
        if ($request->get('page') !== null) {
            $page = $request->get('page');
        }
        $pathToResource = config('app.resource_url_path');
		$listAccount = $this->accountService->search($name, $phone, $page, $gender);
		$tmp = $listAccount->map(function ($item) {
                return [
                	'id' => $item->id,
                    'ten' => $item->ten,
                    'sdt' => $item->sdt,
                    'ngay_sinh' => isset($item->ngay_sinh) ?$item->ngay_sinh:null,
                    'gioi_tinh' => $item->gioi_tinh,
                    'diem_tich' => $item->diem_tich,
                    'email' => $item->email,
                    'da_xoa' => $item->da_xoa,
                    'avatar' => $item->avatar,
                ];
            });
		for ($i=0; $i < count($listAccount); $i++) { 
			$listAccount[$i]->ngay_sinh = date_format(Carbon::parse($listAccount[$i]->ngay_sinh),'d-m-Y');
            $listAccount[$i]->pathToResource = $pathToResource;
        }
		return response()->json(['listSearch'=>$listAccount, 'infoExportExcel'=> $infoExportExcel]);
	}

	public function deleteAccount(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 2) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$id = $request->get('id');
		$result = $this->accountService->delete($id);
		if ($result != 0) {
			 return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
		}
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
	}

	public function twoDigitNumber($number) {
		return $number < 10 ? '0'.$number : $number;
    }

    public function exportAccount(Request $request) {
    	$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 2) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
        $name = $request->get('name');
        $phone = $request->get('phone');
        $gender = $request->get('gender');
        return Excel::download(new AccountExport($name, $phone, $gender), 'account-t&t.xlsx');
    }

	public function editAccount(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->id == 2) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$avatar_path = $request->get('files_edit');
		$ten = $request->get('ten');
		$id = $request->get('id');
		$SDT = $request->get('SDT');
		$NS = $request->get('NS');
		$gender = $request->get('gender');
		$diemtich = $request->get('diemtich');
		$diachi = $request->get('diachi');
		$email = $request->get('email');
		$now = Carbon::now();
		if ($request->file('files_edit') != null || $request->file('files_edit') != '') {
                $subName = 'images/user/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename =  $subName . '/' . $request->file('files_edit')->getClientOriginalName();
                $check = $request->file('files_edit')->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
        }
        $result = $this->accountService->editUser($avatar_path, $ten, $id, $SDT, $NS, $gender, $diemtich, $diachi, $email, $now);
        if ($result == 1) {
        	return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        }else{
        	return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
	}
}
