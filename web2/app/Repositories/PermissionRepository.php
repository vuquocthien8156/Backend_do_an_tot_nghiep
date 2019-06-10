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
		$result = DB::table('users as us')->select('us.id', 'ten', 'email')
		->join('PhanQuyen','PhanQuyen.tai_khoan','=', 'us.id')
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

	public function listPermissionUser() {
		$result = DB::table('PhanQuyen')->select('tai_khoan', 'ten', 'sdt', 'email')
		->join('users as us','PhanQuyen.tai_khoan','=', 'us.id')
		->where([
			'us.da_xoa' => 0,
		])->DISTINCT()->paginate(15); 
		return $result;
	}

	public function getRoll($id) {
		$result = DB::table('PhanQuyen')->select('ten_vai_tro')->leftjoin('vaitro as vt', 'vt.id', '=','PhanQuyen.quyen_cho_phep')->where('tai_khoan' , '=', $id)->get();
        return $result;
	}

	public function inserUser($name, $phone, $email, $password) {
		$result = DB::table('users')
		->insert([
            'ten' => $name,
            'sdt' => $phone,
            'email' => $email,
            'password' => $password,
            'da_xoa' => 0
        ]);
        return $result;
	}

	public function getMaxId() {
		$result = DB::table('users')->max('id');
        return $result;
	}

	public function inserPermission($getMaxId, $permission_group) {
		$result = DB::table('PhanQuyen')
		->insert([
            'tai_khoan' => $getMaxId,
            'quyen_cho_phep' => $permission_group,
            'da_xoa' => 0
        ]);
		return $result;
	}

	public function deletePermission($id) {
		$result = DB::table('PhanQuyen')->where('tai_khoan' , '=', $id)->delete();
		return $result;
	}
}