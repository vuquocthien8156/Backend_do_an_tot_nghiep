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

class LoginRepository {
	public function __construct(SellingVehicle $SellingVehicle) {
		$this->SellingVehicle = $SellingVehicle;
	}

	public function login($user, $pass) {
		$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'password', 'vaitro', 'type')
        ->leftjoin('phanquyen as per', 'per.id_user', '=', 'us.id')
        ->leftjoin('quyen as pe', 'pe.id', '=', 'per.id_quyen')->where('email','=', $user)->where('password','=', $pass)->where('us.trang_thai','=', 1)->get();
		return $result;
	}

	public function loginsdt($user) {
		$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'password', 'vaitro', 'type')
        ->leftjoin('phanquyen as per', 'per.id_user', '=', 'us.id')
        ->leftjoin('quyen as pe', 'pe.id', '=', 'per.id_quyen')->where('sdt','=', $user)->where('us.trang_thai','=', 1)->get();
		return $result;
	}

	public function check($user) {
		$result = DB::table('users as us')->select('us.id as user_id', 'email', 'password', 'vaitro', 'type')
        ->leftjoin('phanquyen as per', 'per.id_user', '=', 'us.id')
        ->leftjoin('quyen as pe', 'pe.id', '=', 'per.id_quyen')->where('email','=', $user)->where('us.trang_thai','=', 1);
        if ($user != '' && $user != null) {
            $result->where(function($where) use ($user) {
                $where->whereRaw('lower(us.email) like ? ', ['%' . trim(mb_strtolower($user, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($user, 'UTF-8')) . '%']);
                });
        } 
		return $result->get();
	}

}