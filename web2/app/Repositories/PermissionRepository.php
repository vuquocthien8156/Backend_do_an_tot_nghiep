<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Enums\EVehicleStatus;
use App\Enums\EVehicleAccredited;
use App\Enums\EVehicleDisplayOrder;
use App\Models\Users;
use App\Models\quyen;
use App\Models\phanquyen;
use App\Models\SellingVehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermissionRepository {
	public function __construct(SellingVehicle $SellingVehicle) {
		$this->SellingVehicle = $SellingVehicle;
	}

	

}