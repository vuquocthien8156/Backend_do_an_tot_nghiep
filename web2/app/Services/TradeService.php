<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\TradeRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class TradeService {
    protected $tradeRepository;

    public function __construct(TradeRepository $tradeRepository) {
        $this->tradeRepository = $tradeRepository;
    }

    public function searchTrade($from_date, $employees, $customer_name, $customer_phone, $to_date, $vehicle_number, $name_store, $page = 1, $pageSize = 15) {
        return $this->tradeRepository->searchTrade($from_date, $employees, $customer_name, $customer_phone, $to_date, $vehicle_number, $name_store, $page = 1, $pageSize = 15);
    }

    public function showDetail($id_order_detail) {
        return $this->tradeRepository->showDetail($id_order_detail);
    }

    public function feedbackTrade($id_feedback, $content_feedback, $created_by) {
    	return $this->tradeRepository->feedbackTrade($id_feedback, $content_feedback, $created_by);	
    }

    public function DeleteTradingRequest($idDelete) {
    	return $this->tradeRepository->DeleteTradingRequest($idDelete);
    }

    public function deleteFeedbackRequest($id_delete_feedback) {
        return $this->tradeRepository->deleteFeedbackRequest($id_delete_feedback);
    }

    public function getStaff($staff) {
        return $this->tradeRepository->getStaff($staff);
    }

    public function getBranch() {
        return $this->tradeRepository->getBranch();
    }
}