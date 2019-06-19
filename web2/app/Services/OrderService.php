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

	public function listOrder($code) {
		return $this->orderRepository->listOrder($code);
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

    public function delete($id) {
        return $this->orderRepository->delete($id);   
    }
}