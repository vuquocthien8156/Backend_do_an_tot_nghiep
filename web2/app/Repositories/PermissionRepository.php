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
		->join('phanquyen','id_user','=', 'us.id')
		->leftjoin('vaitro as vt', 'vt.id', '=','phanquyen.vaitro')
		->where([
			'us.trang_thai' => 1,
		])->orderby('id','asc')->get(); 
		return $result;
	}

	public function Permission($id_per, $id_user) {
		$result = DB::table('phanquyen')
		->where([
			'id_user' => $id_user,
		])->update(['vaitro' => $id_per]); 
		return $result;
	}
}