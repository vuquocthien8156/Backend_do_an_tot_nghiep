<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Enums\EVehicleStatus;
use App\Enums\EVehicleAccredited;
use App\Enums\EOrderType;
use App\Models\Users;
use App\Models\Orders;
use App\Models\Review;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TradeRepository {
	public function __construct(Orders $Orders) {
		$this->Orders = $Orders;
	}

	public function feedbackTrade($id_feedback, $content_feedback, $created_by) {
		try {
            $result = DB::table('review')
            			->where([
            					'table_id' => $id_feedback, 
            					'status' => EStatus::ACTIVE
            			])->update(['content' => $content_feedback]);
            if ($result == 0) {
                $now = Carbon::now();
                $result = new Review;
                $result->table_name = 'orders';
                $result->table_id = $id_feedback;
                $result->content = $content_feedback;
                $result->status = EStatus::ACTIVE;
                $result->created_by = $created_by;
                $result->created_at = $now;
                $result->save();
            }
            return $result;
        } catch (\Exception $e) {
            logger("Failed to update. message: " . $e->getMessage());
            return null;
        }
	}

	public static function getNameEmployeesIdExport($id_employees) {
		$result = DB::table('users')->select('name')->where(['id' => $id_employees])->get();
		return $result;
	}

	public function getStaff($staff) {
        $result = DB::table('users')->select('id','name')
            ->where([['id', '=', $staff],['status', '=', EStatus::ACTIVE]])->get();
        return $result;
    }

    public function getBranch() {
		$result = DB::table('branch')->select('id', 'name')
			->where([
				'status' => EStatus::ACTIVE,
			])->orderBy('id', 'asc')->get();
		return $result;
	}

	public function DeleteTradingRequest($idDelete) {
		try {
			$result = DB::table('review')->where('table_id','=',$idDelete)->update(['content' => '']);
			return $result;
			} catch (\Exception $e) {
				logger("Failed to delete. message: " . $e->getMessage());
				return null;
			}	
	}

	public function deleteFeedbackRequest($id_delete_feedback) {
		try {
			$result = DB::table('review')->where('table_id','=',$id_delete_feedback)->update(['status' => EStatus::DELETED]);
			return $result;
			} catch (\Exception $e) {
				logger("Failed to delete. message: " . $e->getMessage());
				return null;
			}	
	}

	public function searchTrade($from_date, $employees, $customer_name, $customer_phone, $to_date, $vehicle_number, $name_store, $page = 1, $pageSize = 15) {
		$result = DB::table('orders')
                    ->select('od.name as description', 'orders.code as code', 'orders.price as price', 'orders.status', 'us.name as user_name', 'us.phone as user_phone', 
                             'orders.vehicle_number as vehicle_number', 're.content as feedback', 'orders.id as orders_id', 'orders.completed_by as completed_by', 'orders.created_at as order_created_at','use.name as staff', 'orders.odo_km as kilometer', 'br.name as branch_user')
					->join('order_detail as od', 'od.order_id', '=', 'orders.id')
					->join('users as us', 'us.id', '=', 'orders.user_id')  
                    ->leftjoin('users as use', 'use.id', '=','orders.completed_by')
                    ->leftJoin('review as re', 're.table_id', '=', DB::raw('orders.id and re.status = 1'))
                    ->leftJoin('branch_staff as bs', 'us.id', '=','bs.staff_id')
                    ->leftJoin('branch as br', 'br.id', '=', 'orders.branch_id');
		
		if ($customer_phone != '' && $customer_phone != null) {
            $result->where(function($where) use ($customer_phone) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($customer_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($customer_phone, 'UTF-8')) . '%']);
                });
        } 

		if ($employees != '' && $employees != null) {
			$result->where(function($where) use ($employees) {
				$where->whereRaw('lower(use.name) like ? ', ['%' . trim(mb_strtolower($employees, 'UTF-8')) . '%']);
			});
		}

		if ($vehicle_number != '' && $vehicle_number != null) {
			$result->where(function($where) use ($vehicle_number) {
				$where->whereRaw('lower(orders.vehicle_number) like ? ', ['%' . trim(mb_strtolower($vehicle_number, 'UTF-8')) . '%']);
			});
		}

		if ($name_store != '' && $name_store != null) {
            $result->where('br.id', '=', $name_store);
        }

		if ($from_date != '' && $from_date != null) {
            $result->where('orders.created_at', '>', $from_date);
        }

        if ($to_date != '' && $to_date != null) {
            $result->where('orders.created_at', '<', $to_date);
        }

		$result = $result->where([['od.type', '=', EOrderType::ARBITRARY_SERVICE_ORDER]])->orderBy('orders.id', 'desc')->paginate(15); 
		return $result;
	}

	public function showDetail($id_order_detail) {
		$result = DB::table('order_detail')->select('name as service', 'price as price_total', 'quantity', 'meta')->where('order_id', '=', $id_order_detail)->where('type', '=', EOrderType::REPLACEABLE_ITEM)->get();
        return $result;
	}
}