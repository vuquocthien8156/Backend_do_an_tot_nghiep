<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Enums\EVehicleStatus;
use App\Enums\EVehicleAccredited;
use App\Enums\EVehicleDisplayOrder;
use App\Models\Users;
use App\Models\SellingVehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleRepository {
	public function __construct(SellingVehicle $SellingVehicle) {
		$this->SellingVehicle = $SellingVehicle;
	}

	public function loadSellingRequestResource($id) {
		$result = DB::table('selling_vehicle_resource as svr')->select('svr.path_to_resource','svr.selling_vehicle_id','svr.kind')->where('svr.selling_vehicle_id', '=', $id)->where('svr.status' ,'=',1)->orderBy('svr.id', 'desc')->get(); 
		return $result;
	}

	public function searchVehicle($manufacture_selling, $model,$selling_status, $poster_selling, $code, $page = 1, $pageSize = 15) {

		$result = DB::table('selling_vehicle as sv')->join('users as us', 'us.id', '=', 'sv.seller_id')  ->join('category as cat', 'cat.id', '=', 'sv.vehicle_manufacture_id')->select('sv.id as selling_vehicle_id','sv.vehicle_manufacture_id','sv.vehicle_model_id', 'sv.name as title', 'sv.status as selling_status', 'sv.price', 'sv.description', 'us.name as poster_name', 'cat.name as manufacture_selling','sv.accredited','us.phone','sv.display_order','sv.code', 'sv.approved');


		if ($manufacture_selling != '' && $manufacture_selling != null) {
			$result->where('sv.vehicle_manufacture_id', '=', $manufacture_selling);
		}

		if ($model != '' && $model != null) {
			$result->where('sv.vehicle_model_id', '=', $model);
		}

		if ($poster_selling != '' && $poster_selling != null) {
			 $result->where(function($where) use ($poster_selling) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($poster_selling, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($poster_selling, 'UTF-8')) . '%']);
                });
		}

		if ($selling_status != '' && $selling_status != null) {
			$result->where('sv.status', '<>', EStatus::DELETED);
			$result->where('cat.status', '<>', EStatus::DELETED);
		} else {
			$result->where('sv.status', '<>', EStatus::DELETED);
			$result->where('cat.status', '<>', EStatus::DELETED);
		}

		if ($selling_status != '' && $selling_status != null) {
			$result->where('sv.status', '=', $selling_status);
		}

		if ($code != '' && $code != null) {
			 $result->where(function($where) use ($code) {
                $where->whereRaw('lower(sv.code) like ? ', ['%' . trim(mb_strtolower($code, 'UTF-8')) . '%']);
            });
		}

		$result = $result->orderBy('sv.display_order','desc')->orderBy('sv.status','asc')->orderBy('sv.id', 'desc')->paginate(15); 
		return $result;
	}

	public function accreditedSellingRequest($idVehicle, $accredited_by) {
		try {
			$now = Carbon::now();
			$result = DB::table('selling_vehicle')->where('id','=',$idVehicle)
					->update(['accredited' => TRUE, 'accredited_by' => $accredited_by, 'accredited_at' => $now]);
			return $result;
			} catch (\Exception $e) {
				logger("Failed to delete Customer. message: " . $e->getMessage());
				return null;
			}
	}	
	public static function getNameManufactureByIdExport($id_category) {
		$result = DB::table('category')->select('name')->where(['id' => $id_category])->get();
		return $result;
	}

	public function getManufacture() {
		$result = DB::table('category')->select('id', 'name')
			->where([
				'status' => EStatus::ACTIVE,
				'type' => EManufacture::MANUFACTURE,
			])->orderBy('seq', 'asc')->get();
		return $result;
	}

	public function getManufactureModel($id_manufacture) {
		$result = DB::table('category')->select('id', 'name')
			->where([
				'status' => EStatus::ACTIVE,
				'type' => EManufacture::MANUFACTURE_MODEL,
				'parent_category_id' => $id_manufacture,
			])->orderBy('seq', 'asc')->get();
		return $result;
	}

	public function getNameManufactureById($id_category) {
		$result = DB::table('category')->select('name')->where(['id' => $id_category])->get();
		return $result;
	}

	public function updateStatus($selling_id){
		$result = DB::table('selling_vehicle')->where(['id' => $selling_id])->update(['status' => EVehicleStatus::SOLD,'display_order'=>EVehicleDisplayOrder::NOPRIORITIZE]);
		return $result;
	}

	public function updateprioritize($id,$order){
		try {
			if($order == EVehicleDisplayOrder::PRIORITIZE){
				$result = DB::table('selling_vehicle')->where(['id' => $id])->update(['display_order' =>  EVehicleDisplayOrder::NOPRIORITIZE]);
				return $result;
			}
			else{	
				$result = DB::table('selling_vehicle')->where(['id' => $id])->update(['display_order' =>  EVehicleDisplayOrder::PRIORITIZE]);
				return $result;
		}
		} catch (Exception $e) {
			return null;	
		}
		
	}

	public function updateApproved($id_selling ,$approved_by) {
		try {
			$now = Carbon::now();
		 	$result = DB::table('selling_vehicle')->where(['id' => $id_selling])
                     ->update([ 'approved' => TRUE,
                                'status' => EStatus::ACTIVE,
                                'approved_at' => $now, 
		 				 		'approved_by' => $approved_by
		 				 	]);
		 	return $result;
		 } catch (Exception $e) {
		 	logger("Failed to updateApproved vehicle. message: " . $e->getMessage());
			return null;
		 } 
	}
}