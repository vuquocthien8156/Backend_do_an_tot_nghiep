<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\OrderRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class OrderService {
	protected $loginRepository;

	public function __construct(OrderRepository $orderRepository) {
		$this->orderRepository = $orderRepository;
	}

	public function listOrder($id, $code) {
		return $this->orderRepository->listOrder($id, $code);
	}

    public function getNameStatus($id) {
        return $this->orderRepository->getNameStatus($id);
    }

    public function statusOrder($id) {
        return $this->orderRepository->statusOrder($id);
    }

	public function getStatus() {
		return $this->orderRepository->getStatus();
	}

	public function updateStatus($id, $status) {
		return $this->orderRepository->updateStatus($id, $status);
	}

	public function getPoint($ma_kh) {
        return $getPoint = $this->orderRepository->getPoint($ma_kh);
    }

    public function addPoint($ma_kh, $totalPoint) {
        return $addPoint = $this->orderRepository->addPoint($ma_kh, $totalPoint);   
    }

    public function delete($id,$status) {
        return $this->orderRepository->delete($id,$status);   
    }

    public function editOrder($thong_tin_giao_hang, $ten_khuyen_mai, $phi_ship, $ngay_lap, $tong_tien, $ghi_chu, $phuong_thuc_thanh_toan, $id) {
        return $this->orderRepository->editOrder($thong_tin_giao_hang, $ten_khuyen_mai, $phi_ship, $ngay_lap, $tong_tien, $ghi_chu, $phuong_thuc_thanh_toan, $id);   
    }

    public function getAllDisCount() {
    	return $this->orderRepository->getAllDisCount();
    }

    public function detailOrder($id) {
    	return $this->orderRepository->detailOrder($id);
    }
}