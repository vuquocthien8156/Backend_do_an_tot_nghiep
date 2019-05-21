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
		$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'gioi_tinh' ,'sdt', 'diem_tich' , 'ngay_sinh' , 'dia_chi' , 'fb_id' , 'avatar' , 'id_vai_tro', 'quyen_he_thong' )
        ->leftjoin('PhanQuyen as per', 'per.tai_khoan', '=', 'us.id')
        ->leftjoin('quyen as pe', 'pe.ma_so', '=', 'per.quyen_cho_phep')
        ->where(['email' => $user, 
        		'password' => $pass, 
        		'us.da_xoa' => 0])->get();
		return $result;
	}

	public function getInfoByEmail($email) {
		$result = DB::table('users')->select('id as user_id','ten', 'email', 'sdt' , 'gioi_tinh', 'fb_id', 'diem_tich' , 'ngay_sinh' , 'avatar')->where('email', '=', $email)->get();
		return $result;
	}

	public function loginsdt($user) {
		$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'gioi_tinh' ,'sdt', 'diem_tich' , 'ngay_sinh' , 'dia_chi' , 'fb_id' , 'avatar' , 'id_vai_tro', 'quyen_he_thong' )
        ->leftjoin('PhanQuyen as per', 'per.tai_khoan', '=', 'us.id')
        ->leftjoin('quyen as pe', 'pe.ma_so', '=', 'per.quyen_cho_phep')->where('sdt','=', $user)->where('us.da_xoa','=', 0)->get();
		return $result;
	}

	public function check($user) {
		$result = DB::table('users as us')->select('us.id as user_id', 'email', 'password', 'sdt', 'fb_id');
        if ($user != '' && $user != null) {
            $result->where(function($where) use ($user) {
                $where->where('us.email', '=' ,$user)
                    ->orWhere('us.sdt' , '=' ,$user)
                    ->orWhere('us.fb_id' , '=' , $user);
                });
        } 
		return $result->get();
	}
	
	public function updateInfo($email, $name, $phone, $gender, $dob, $avatar, $id) {
		$result = DB::table('users')->where('id', '=', $id)
		->update([
			'email' => $email,
			'ten' => $name,
			'sdt' => $phone,
			'gioi_tinh' => $gender,
			'ngay_sinh' =>$dob,
			'avatar' => $avatar
		]);
		return $result;
	}

	public function getLikedProduct($id) {
		$result = DB::table('SanPhamYeuThich')->select(
			'ma_so' ,'ma_chu', 'SanPham.ten', 'gia_san_pham', 'ngay_ra_mat', 'hinh_san_pham'
			 , 'so_lan_dat' , 'gia_vua' , 'gia_lon' , 'mo_ta' )
		->leftjoin('SanPham', 'ma_so', '=', 'ma_san_pham')
		->leftjoin('users', 'id', '=', 'ma_khach_hang')
		->where(['users.id' => $id, 'thich' => 1])->get();
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
           'trang_thai' => 0,
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
		$result = DB::table('users')->select('ten', 'id as user_id', 'email', 'gioi_tinh' ,'sdt', 'diem_tich' , 'ngay_sinh' , 'dia_chi' , 'fb_id' , 'avatar' );
		if ($id_KH != null && $id_KH != '') {
			$result->where('id', '=', $id_KH);
		}
		return $result->get();
	}

	public function updateUserFB($id_fb, $email , $type) {
		if ($type == 4) {
			$result = DB::table('users')->where('email', '=', $email)
			->update([
				'fb_id' => $id_fb,
			]);
		}
		else
		{
			$result = DB::table('users')->where('fb_id', '=', $id_fb)
			->update([
				'email' => $email,
			]);
		}
		return $result;
	}

	public function getInfo($id_fb) {
		$result = DB::table('users')->select('ten', 'id as user_id', 'email', 'gioi_tinh' ,'sdt', 'diem_tich' , 'ngay_sinh' , 'dia_chi' , 'fb_id' , 'avatar' )->where('fb_id', '=', $id_fb)->get();
		return $result;
	}

	public function loginfb($id_fb) {
		
			$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'gioi_tinh' ,'sdt', 'diem_tich' , 'ngay_sinh' , 'dia_chi' , 'fb_id' , 'avatar' , 'id_vai_tro', 'quyen_he_thong')
	        ->leftjoin('PhanQuyen as per', 'per.tai_khoan', '=', 'us.id')
	        ->leftjoin('quyen as pe', 'pe.ma_so', '=', 'per.quyen_cho_phep')
	        ->where([
	        		'fb_id' => $id_fb, 
	        		'us.da_xoa' => 0])->get();
			return $result;
	}

	public function create($id_fb, $email , $name) {
		if ($email != null && $email != '') {
			$result = DB::table('users')->insert([
						   'email' => $email,
				           'fb_id' => $id_fb,
				           'ten' => $name,
				           'da_xoa' => 0,
       			 ]);
		}else {
			$result = DB::table('users')->insert([
				           'fb_id' => $id_fb,
				           'ten' => $name,
				           'da_xoa' => 0,
       			 ]);
		}
        return $result;	
	}

	public function news($page) {
		if ($page == null) {
            $result = DB::table('TinTuc')->select('ten_tin_tuc', 'noi_dung', 'ngay_dang', 'hinh_tin_tuc', 'ngay_tao')
	        ->where([
	            'da_xoa' => 0,
	        ]);
        	return $result->limit(4)->orderBy('ngay_tao', 'desc')->get();
        }else {
            $result = DB::table('TinTuc')->select('ten_tin_tuc', 'noi_dung', 'ngay_dang', 'hinh_tin_tuc', 'ngay_tao')
	        ->where([
	            'da_xoa' => 0,
	        ]);
        	return $result->limit(4)->orderBy('ngay_tao', 'desc')->paginate(4);
        }
	}

	public function idMax() {
		$result = DB::table('users')->max('id');
		return $result;
	}

	public function productType($page) {
		if ($page == null) {
            $result = DB::table('LoaiSanPham')->select('ma_loai_sp', 'ten_loai_sp', 'loai_chinh')
	        ->where([
	            'da_xoa' => 0,
	        ]);
        	return $result->orderBy('ma_loai_sp', 'asc')->get();
        }else {
            $result = DB::table('LoaiSanPham')->select('ma_loai_sp', 'ten_loai_sp', 'loai_chinh')
	        ->where([
	            'da_xoa' => 0,
	        ]);
        	return $result->orderBy('ma_loai_sp', 'asc')->paginate(15);
        }
	}

	public function insertCart($id_KH, $id_sp, $size, $so_luong, $parent_id) {
		$result = DB::table('GioHang')->insert([
           'ma_khach_hang' => $id_KH,
           'ma_san_pham' => $id_sp,
           'kich_co' => $size,
           'so_luong' => $so_luong,
           'parent_id' => $parent_id
        ]);
        return $result;	
	}

	public function getCart($id_KH) {
		$result = DB::table('GioHang')->select( 'ma_gio_hang' , 'ma_khach_hang', 'ma_san_pham', 'so_luong', 'kich_co', 'parent_id')->where('ma_khach_hang' , '=', $id_KH)->get();
        return $result;	
	}

	public function deleteCart($id_GH) {
		$result = DB::table('GioHang')->where('ma_gio_hang' ,'=', $id_GH)->delete();
		$result1 = DB::table('GioHang')->where('parent_id' ,'=', $id_GH)->delete();
        
        return $result + $result1;	
	}

	public function deleteCartCustomer($id_KH) {
		$result = DB::table('GioHang')->where('ma_khach_hang' , '=', $id_KH)
									 ->delete();
        return $result;	
	}

	public function getQuantity($id_GH) {
		$result = DB::table('GioHang')->select('so_luong')->where('ma_gio_hang' , '=', $id_GH)->get();
        return $result;	
	}
	
	public function updateQuantity($id_GH, $sl, $type) {
		if ($type == 1) {
			$sl = $sl+1;
			$result = DB::table('GioHang')->where('ma_gio_hang' , '=', $id_GH)->update(['so_luong' => $sl]);
        	return $result;	
		}
		if ($type == 2) {
			$sl = $sl-1;
			$result = DB::table('GioHang')->where('ma_gio_hang' , '=', $id_GH)->update(['so_luong' => $sl]);
        	return $result;		
		}
	}

	public function getProductById($idProduct){
		
	}
}