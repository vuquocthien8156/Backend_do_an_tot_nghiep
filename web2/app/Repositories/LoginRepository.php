<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use App\Models\quyen;
use App\Models\phanquyen;
use Illuminate\Support\Carbon;
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
<<<<<<< HEAD
                      ->orWhereRaw('lower(us.sdt) like ? ', ['%' . trim(mb_strtolower($user, 'UTF-8')) . '%']);
=======
                     ->orWhereRaw('lower(us.sdt) like ? ', ['%' . trim(mb_strtolower($user, 'UTF-8')) . '%']);
>>>>>>> 47a5dcf9095712c128e3787f80f70178e2990e0e
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

	public function getLikedProduct($email) {
		$result = DB::table('SanPhamYeuThich')->select('ma_chu', 'SanPham.ten', 'gia_san_pham', 'ngay_ra_mat', 'hinh_san_pham')
		->leftjoin('SanPham', 'ma_so', '=', 'ma_san_pham')
		->leftjoin('users', 'id', '=', 'ma_khach_hang')
		->where('email', '=', $email)->get();
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
}