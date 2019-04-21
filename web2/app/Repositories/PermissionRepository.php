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
use App\Models\vaitro;
use App\Models\phanquyen;
use App\Models\SellingVehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermissionRepository {
	public function __construct(SellingVehicle $SellingVehicle) {
		$this->SellingVehicle = $SellingVehicle;
	}

	public function getListPermission() {
		$result = DB::table('vaitro')->select('id', 'ten_vai_tro')->where([
			'trang_thai' => 1,
		])->orderby('id','asc')->get(); 
		return $result;
	}

	public function getListInternalUser() {
		$result = DB::table('users as us')->select('us.id', 'ten')
		->join('phanquyen','id_user','=', 'us.id')
		->where([
			'us.trang_thai' => 1,
		])->orderby('id','asc')->get(); 
		return $result;
	}
}