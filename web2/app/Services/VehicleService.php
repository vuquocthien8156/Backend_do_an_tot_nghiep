<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\VehicleRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class VehicleService {
	protected $vehicleRepository;

	public function __construct(VehicleRepository $vehicleRepository) {
		$this->vehicleRepository = $vehicleRepository;
	}
	
	public function searchVehicle($manufacture_selling, $model, $selling_status, $poster_selling, $code, $page = 1, $pageSize = 15) {
		return $this->vehicleRepository->searchVehicle($manufacture_selling, $model, $selling_status, $poster_selling, $code, $page = 1, $pageSize = 15);
	}

	public function accreditedSellingRequest($idVehicle, $accredited_by) {
		return $this->vehicleRepository->accreditedSellingRequest($idVehicle, $accredited_by);
	}

	public function doExportExcelVehicle($manufacture_selling, $selling_status, $model, $poster_selling) {
		return $this->vehicleRepository->doExportExelVehicle($manufacture_selling, $model, $selling_status, $poster_selling);
	}

	public function loadSellingRequestResource($id) {
		return $this->vehicleRepository->loadSellingRequestResource($id);
	}

	public function getManufacture() {
		return $this->vehicleRepository->getManufacture();
	}

	public function getManufactureModel($id_manufacture) {
		return $this->vehicleRepository->getManufactureModel($id_manufacture);
	}

	public function getNameManufactureById($category_id) {
		return $this->vehicleRepository->getNameManufactureById($category_id);
	}

	public function updateStatus($selling_id){
		return $this->vehicleRepository->updateStatus($selling_id);
	}

	public function updateprioritize($id,$order){
		return $this->vehicleRepository->updateprioritize($id,$order);
	}

	public function updateApproved($id_selling, $approved_by) {
		return $this->vehicleRepository->updateApproved($id_selling, $approved_by);
	}

}