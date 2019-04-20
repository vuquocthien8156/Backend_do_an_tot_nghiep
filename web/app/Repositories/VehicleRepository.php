<?php

namespace App\Repositories;

use App\Models\Users;
use App\Models\SellingVehicle;
use App\Models\taikhoan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleRepository {
	public function __construct(SellingVehicle $SellingVehicle) {
		$this->SellingVehicle = $SellingVehicle;
	}

	
}