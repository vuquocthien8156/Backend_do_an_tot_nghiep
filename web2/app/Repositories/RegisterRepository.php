<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use App\Models\phanquyen;
use App\Models\quyen;
use App\Models\taikhoan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RegisterRepository {

	public function login($user, $pass) {
		$result = DB::table('taikhoan')->select('email', 'mat_khau')->where([
			'email'=> $user,
			'mat_khau' => $pass,
			'trang_thai' => 0,
		])->get(); 
		return $result;
	}

	public function getAccount($username) {
		$result = DB::table('users')->select('email')->where([
			'da_xoa' => 0,
		])->where('email' , '=', $username)->get(); 
		return $result;
	}

	public function getPhone($username) {
		$result = DB::table('users')->select('sdt')->where([
			'da_xoa' => 0,
		])->where('sdt' , '=', $username)->get(); 
		return $result;
	}

	public function insertUser($username, $password, $name, $gender, $birthday, $phone, $address) {
		$user = new Users();
		$user->ten = $name;
		$user->ngay_sinh = $birthday;
		$user->gioi_tinh = $gender;
		$user->sdt = $phone;
		$user->dia_chi = $address;
		$user->email = $username;
		$user->password = $password;
		$user->da_xoa = 0;
		$user->save();
		return $user;
	}

	public function insertUserPhone($username, $password, $name, $gender, $birthday, $address) {
		$user = new Users();
		$user->ten = $name;
		$user->ngay_sinh = $birthday;
		$user->gioi_tinh = $gender;
		$user->sdt = $username;
		$user->dia_chi = $address;
		$user->password = $password;
		$user->da_xoa = 0;
		$user->save();
		return $user;
	}

	public function idMax() {
		$result = DB::table('users')->max('id');
		return $result;
	}

	public function insertPermission($idMax) {
		$result = DB::table('phanquyen')->insert(['id_user' => $idMax, 'trang_thai' => 0]);
		// $phanquyen = new phanquyen();
		// $phanquyen->id_user = $idMax;
		// $phanquyen->id_quyen = 1;
		return $result;
	}
}