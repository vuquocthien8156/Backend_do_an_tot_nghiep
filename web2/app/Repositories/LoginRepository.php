<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use App\Models\quyen;
use App\Models\phanquyen;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LoginRepository {

	public function login($user, $pass) {
		$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'password', 'id_vai_tro', 'quyen_he_thong')
        ->leftjoin('PhanQuyen as per', 'per.tai_khoan', '=', 'us.id')
        ->leftjoin('quyen as pe', 'pe.ma_so', '=', 'per.quyen_cho_phep')->where('email','=', $user)->where('password','=', $pass)->where('us.da_xoa','=', 1)->get();
		return $result;
	}

	public function loginsdt($user) {
		$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'password', 'vaitro', 'quyen_he_thong')
        ->leftjoin('phanquyen as per', 'per.id_user', '=', 'us.id')
        ->leftjoin('quyen as pe', 'pe.id', '=', 'per.id_quyen')->where('sdt','=', $user)->where('us.trang_thai','=', 1)->get();
		return $result;
	}

	public function check($user) {
		$result = DB::table('users as us')->select('us.id as user_id', 'email', 'password', 'sdt', 'fb_id');
        if ($user != '' && $user != null) {
            $result->where(function($where) use ($user) {
                $where->whereRaw('lower(us.email) like ? ', ['%' . trim(mb_strtolower($user, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.sdt) like ? ', ['%' . trim(mb_strtolower($user, 'UTF-8')) . '%']);
                });
        } 
		return $result->get();
	}
	public function updateInfo($email, $name, $phone, $gender, $dob, $avatar) {
		$result = DB::table('users')->where('email', '=', $email)
		->update([
			'ten' => $name,
			'sdt' => $phone,
			'gioi_tinh' => $gender,
			'ngay_sinh' =>$dob,
			'avatar' => $avatar
		]);
		return $result;
	}

	public function getLikedProduct($id) {
		$result = DB::table('SanPhamYeuThich')->select('ma_chu', 'SanPham.ten', 'gia_san_pham', 'ngay_ra_mat', 'hinh_san_pham')
		->leftjoin('SanPham', 'ma_so', '=', 'ma_san_pham')
		->leftjoin('users', 'id', '=', 'ma_khach_hang')
		->where('users.id', '=', $id)->get();
		return $result;
	}

	public function getLike() {
		$result = DB::table('SanPhamYeuThich')->select('ma_san_pham', 'ma_khach_hang', 'thich')->get();
		return $result;	
	}

	public function updateLike($id_product, $id_user, $like) {
		$result = DB::table('SanPhamYeuThich')->where(['ma_san_pham' => $id_product, 'ma_khach_hang' => $id_user])->update(['thich' => $like]);
		return $result;	
	}

	public function insertLike($id_product, $id_user, $like) {
		$result = DB::table('SanPhamYeuThich')->insert([
           'ma_san_pham' => $id_product,
           'ma_khach_hang' => $id_user,
           'thich' => $like,
           'trang_thai' => 1,
        ]);
        return $result;	
	}

	public function getAllOrder($id_KH) {
		$result = DB::table('DonHang')->select('ma_don_hang', 'ma_khach_hang', 'ma_khuyen_mai', 'ngay_lap', 'phi_ship', 'tong_tien', 'ghi_chu');
		if ($id_KH != null && $id_KH != '') {
			$result->where('ma_khach_hang', '=', $id_KH);
		}
		return $result->get();
	}

	public function getUser($id_KH) {
		$result = DB::table('users')->select('id', 'ten', 'sdt', 'dia_chi');
		if ($id_KH != null && $id_KH != '') {
			$result->where('id', '=', $id_KH);
		}
		return $result->get();
	}

	public function updateIdFB($id_fb, $email) {
		$result = DB::table('users')->where('email', '=', $email)
		->update([
			'fb_id' => $id_fb,
		]);
		return $result;
	}

	public function getInfo($id_fb) {
		$result = DB::table('users')->select('ten', 'email', 'sdt' , 'gioi_tinh', 'ngay_sinh', 'password')->where('fb_id', '=', $id_fb)->get();
		return $result;
	}
	public function insertPass($id_fb) {
		$pass = Hash::make(123);
		$result = DB::table('users')->where('fb_id', '=', $id_fb)->update([
           'password' => $pass,
        ]);
        return $result;	
	}
}