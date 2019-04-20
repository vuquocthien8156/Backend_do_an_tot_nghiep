<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;
use App\Repositories\VehicleTransferRepository;

class VehicleTransferService {
    protected $vehicleTransferRepository;

	public function __construct(VehicleTransferRepository $vehicleTransferRepository) {
		$this->vehicleTransferRepository = $vehicleTransferRepository;
    }

    public function getUserForwarder() {
        return $this->vehicleTransferRepository->getUserForwarder();
    }

    public function getListBranchStaff($branch_id) {
        return $this->vehicleTransferRepository->getListBranchStaff($branch_id);
    }

    public function assignStaffTransfer($id_vehicle_transfer, $branch_id, $assign_staff_id, $distance, $price, $note, $assigned_staff_by) {
        return $this->vehicleTransferRepository->assignStaffTransfer($id_vehicle_transfer, $branch_id, $assign_staff_id, $distance, $price, $note, $assigned_staff_by);
    }

    public function completeVehicleTransfer($id_vehicle_transfer, $updated_by) {
        return $this->vehicleTransferRepository->completeVehicleTransfer($id_vehicle_transfer, $updated_by);
    }

    public function deleteVehicleTransfer($id_vehicle_transfer, $deleted_by) {
        return $this->vehicleTransferRepository->deleteVehicleTransfer($id_vehicle_transfer, $deleted_by);
    }
}