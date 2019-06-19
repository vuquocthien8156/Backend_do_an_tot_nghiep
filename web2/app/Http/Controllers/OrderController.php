<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Services\OrderService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class OrderController extends Controller {
	use CommonTrait;

	public function __construct(OrderService $orderService) {
		$this->orderService = $orderService;
	}

	public function orderView() {
		return view('order.order');
	}

	public function searchOrder(Request $request) {
		$code = $request->get('code');
		$page = 1;
        if ($request->get('page') !== null) {
                $page = $request->get('page');
        }
        $listOrder = $this->orderService->listOrder($code);
        for ($i=0; $i < count($listOrder); $i++) { 
            $listOrder[$i]->ngay_lap = date_format(Carbon::parse($listOrder[$i]->ngay_lap), 'd-m-Y');
            $listOrder[$i]->tong_tien = number_format($listOrder[$i]->tong_tien);
            $listOrder[$i]->phi_ship = number_format($listOrder[$i]->phi_ship);
        }
		return response()->json(['listSearch'=>$listOrder]);
	}

	public function accepthOrder(Request $request) {
		$id = $request->get('id');
		$ma_kh = $request->get('ma_khach_hang');
		$status = $request->get('status')+1;
		$updateStatus = $this->orderService->updateStatus($id, $status);
		$tong_tien = $request->get('tong_tien');
		$point = (int)$tong_tien / 10000;
		$phuong_thuc = $request->get('phuong_thuc');
		$getPoint = $this->orderService->getPoint($ma_kh);
		$totalPoint = (int)$getPoint[0]->diem_tich + (int)$point;
		if ($status == 5 && $phuong_thuc = 2) {
			$updatePointUser = $this->orderService->addPoint($ma_kh, $totalPoint);
		}
		if ($updateStatus > 0) {
			return response()->json(['error'=>0]);		
		}
		return response()->json(['error'=>1]);
	}

	public function deleteOrder(Request $request) {
		$id = $request->get('id');
		$result = $this->orderService->delete($id);
		if ($result != 0) {
			 return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
		}
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
	}
}
