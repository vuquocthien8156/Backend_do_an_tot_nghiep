<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Services\OrderService;
use App\Services\LoginService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class OrderController extends Controller {
	use CommonTrait;

	public function __construct(OrderService $orderService, LoginService $loginService) {
		$this->orderService = $orderService;
		$this->loginService = $loginService;
	}

	public function orderView(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$getAllDisCount = $this->orderService->getAllDisCount();
		$getStatus = $this->orderService->getStatus();
		return view('order.order',['getAllDisCount' => $getAllDisCount, 'getStatus' => $getStatus]);
	}

	public function searchOrder(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$code = $request->get('code');
		$page = 1;
		$id = null;
        if ($request->get('page') !== null) {
                $page = $request->get('page');
        }
        $listOrder = $this->orderService->listOrder($id, $code);
        //dd($listOrder);
        
        	for ($i=0; $i < count($listOrder); $i++) { 
            $listOrder[$i]->ngay_lap = date_format(Carbon::parse($listOrder[$i]->ngay_lap), 'd-m-Y');
            $listOrder[$i]->tong_tien2 = number_format($listOrder[$i]->tong_tien);
            if($listOrder[$i]->phuong_thuc_thanh_toan == 1)
            {
            	$listOrder[$i]->tien_phai_tra = number_format($listOrder[$i]->tong_tien+$listOrder[$i]->phi_ship);
            }
            if($listOrder[$i]->phuong_thuc_thanh_toan == 3 || $listOrder[$i]->phuong_thuc_thanh_toan == 2)
            {
            	$listOrder[$i]->tien_phai_tra = 0;
            }
            if ($listOrder[$i]->phuong_thuc_thanh_toan == 21) {
            	$listOrder[$i]->tien_phai_tra = number_format($listOrder[$i]->tong_tien+$listOrder[$i]->phi_ship - ($listOrder[$i]->so_diem*10000) - $listOrder[$i]->gia_khuyen_mai);	
            }
            if ($listOrder[$i]->phuong_thuc_thanh_toan == 23) {
            	$listOrder[$i]->tien_phai_tra = 0;	
            }
            $listOrder[$i]->phi_ship2 = number_format($listOrder[$i]->phi_ship);
            $getStatus = $this->orderService->statusOrder($listOrder[$i]->madh);
            $getNameStatus = $this->orderService->getNameStatus($getStatus);
            if (isset($getNameStatus[0]->ma_trang_thai)) {
            	$listOrder[$i]->ten_trang_thai = $getNameStatus[0]->ten_trang_thai;
	            $listOrder[$i]->trang_thai = $getNameStatus[0]->ma_trang_thai;
            }
        	}
                
		return response()->json(['listSearch'=>$listOrder]);
	}

	public function accepthOrder(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$id = $request->get('id');
		$code = null;
		$check = 0;
		for($i = 0; $i < count($id); $i++) {
			$listOrder = $this->orderService->listOrder($id[$i], $code);
			//$a = $listOrder[$i]->madh;
			$getStatus = $this->orderService->statusOrder($listOrder[$i]->madh);
			$status = $getStatus + 1;
			$updateStatus = $this->orderService->updateStatus($id[$i], $status);

			$tong_tien = $listOrder[0]->tong_tien;
			$point = (int)$tong_tien / 10000;
			$phuong_thuc = $listOrder[0]->phuong_thuc_thanh_toan;
			$getPoint = $this->orderService->getPoint($listOrder[0]->ma_khach_hang);
			$totalPoint = (int)$getPoint[0]->diem_tich + (int)$point;
			$ngay_lap = Carbon::now();
			if ($status == 5) {
				$hinh_thuc = 1;
				$updatePointUser = $this->orderService->addPoint($listOrder[0]->ma_khach_hang, (int)$totalPoint);
				$addLog = $this->loginService->addLog($listOrder[0]->ma_khach_hang, $id[$i], $hinh_thuc, $ngay_lap, (int)$point);
				if ($updateStatus > 0) {
					$check = 0;		
				}else {
					$check = 1;
					break;
				}
			}
		}
		if ($check == 0) {
				return response()->json(['error'=>0]);		
			}
			return response()->json(['error'=>1]);
	}

	public function deleteOrder(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$id = $request->get('id');
		$status = $request->get('status');
		if ($status == 1) {
			$status = 0;
		}else {
			$status = 1;
		}
		$result = $this->orderService->delete($id,$status);
		if ($result != 0) {
			 return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
		}
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
	}
	public function editOrder(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$thong_tin_giao_hang = $request->get('thong_tin_giao_hang');
		$ten_khuyen_mai = $request->get('ten_khuyen_mai');
		$phi_ship = $request->get('phi_ship');
		$tong_tien = $request->get('tong_tien');
		$ghi_chu = $request->get('ghi_chu');
		$phuong_thuc_thanh_toan = $request->get('phuong_thuc_thanh_toan');
		$ngay_lap = $request->get('ngay_lap');
		$id = $request->get('id');
		$result = $this->orderService->editOrder($thong_tin_giao_hang, $ten_khuyen_mai, $phi_ship, $ngay_lap, $tong_tien, $ghi_chu, $phuong_thuc_thanh_toan, $id);
		if ($result != 0) {
			 return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
		}
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
	}

	public function detailOrder(Request $request) {
		$per = $request->session()->get('id');
		$check = false;
		for($i = 0; $i < count($per); $i++) {
			if ($per[$i]->quyen_cho_phep == 1) {
				$check = true;
			}
		}
		if($check == false) {
			return "Bạn không có quyền truy cập";
		}
		$id = $request->get('id');
		$result = $this->orderService->detailOrder($id);
		for($i = 0; $i<count($result); $i++) {
			$topping = $this->loginService->getTopping($result[$i]->ma_chi_tiet);
			$result[$i]->topping = $topping;
		}
		return response()->json(['listDetail'=>$result]);
	}
}
