<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use App\Models\quyen;
use App\Models\vaitro;
use App\Models\phanquyen;;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermissionRepository {

	public function getListPermission() {
		$result = DB::table('vaitro')->select('id', 'ten_vai_tro')->where([
			'trang_thai' => 1,
		])->orderby('id','asc')->get(); 
		return $result;
	}

	public function getListInternalUser() {
		$result = DB::table('users as us')->select('us.id', 'ten', 'email', 'ten_vai_tro')
		->join('PhanQuyen','PhanQuyen.tai_khoan','=', 'us.id')
		->leftjoin('vaitro as vt', 'vt.id', '=','PhanQuyen.id_vai_tro')
		->where([
			'us.da_xoa' => 1,
		])->orderby('us.id','asc')->get(); 
		return $result;
	}

	public function Permission($id_per, $id_user) {
		$result = DB::table('PhanQuyen')
		->where([
			'tai_khoan' => $id_user,
		])->update(['id_vai_tro' => $id_per]); 
		return $result;
	}
}