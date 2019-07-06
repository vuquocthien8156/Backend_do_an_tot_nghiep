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
		$result = DB::table('LoaiTaiKhoan')->select('ma_loai_tai_khoan', 'ten_loai_tai_khoan')->where([
			'da_xoa' => 0,
		])->where('ma_loai_tai_khoan', '!=', 1)->orderby('ma_loai_tai_khoan','asc')->get(); 
		return $result;
	}

	public function getListInternalUser() {
		$result = DB::table('users as us')->select('us.id', 'ten', 'email', 'da_xoa')
		->where('loai_tai_khoan', '!=', 1)->orderby('us.id','asc')->get(); 
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
		$result = DB::table('users as us')->select('us.id', 'ten', 'email', 'loai_tai_khoan', 'sdt', 'da_xoa')
		->where('loai_tai_khoan', '!=', 1)->orderby('us.id','asc')->paginate(15); 
		return $result;
	}

	public function getRoll($roll) {
		$result = DB::table('PhanQuyen')->select('ten_vai_tro','quyen_cho_phep')
		->join('vaitro', 'id', '=', 'quyen_cho_phep')
		->join('LoaiTaiKhoan', 'LoaiTaiKhoan.ma_loai_tai_khoan', '=', 'PhanQuyen.ma_loai_tai_khoan')
		->where('LoaiTaiKhoan.ma_loai_tai_khoan' , '=', $roll)->get();
        return $result;
	}

	public function getNamePer($roll) {
		$result = DB::table('LoaiTaiKhoan')->select('ten_loai_tai_khoan')
		->where('LoaiTaiKhoan.ma_loai_tai_khoan' , '=', $roll)->get();
        return $result;
	}

	public function inserUser($name, $phone, $email, $password, $permission_group) {
		$now = Carbon::now();
		$result = DB::table('users')
		->insert([
            'ten' => $name,
            'sdt' => $phone,
            'email' => $email,
            'password' => $password,
            'created_at' => $now,
            'avatar' => 'images/logo1.png',
            'loai_tai_khoan' => $permission_group,
            'da_xoa' => 0
        ]);
        return $result;
	}

	public function getMaxId() {
		$result = DB::table('users')->max('id');
        return $result;
	}

	public function inserPermission($user_id, $permission_group) {
		$result = DB::table('users')
		->where('id', '=', $user_id)
		->update([
            'loai_tai_khoan' => $permission_group,
        ]);
		return $result;
	}

	public function deletePermission($user_id,$status) {
		$result = DB::table('users')->where('id' , '=', $user_id)->update(['da_xoa' => $status]);
		return $result;
	}
}