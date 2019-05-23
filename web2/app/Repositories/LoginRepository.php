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
		$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'password', 'gioi_tinh' ,'sdt', 'diem_tich' , 'ngay_sinh' , 'dia_chi' , 'fb_id' , 'avatar' , 'id_vai_tro', 'quyen_he_thong' )
        ->leftjoin('PhanQuyen as per', 'per.tai_khoan', '=', 'us.id')
        ->leftjoin('quyen as pe', 'pe.ma_so', '=', 'per.quyen_cho_phep')
        ->where(['email' => $user, 
        		'password' => $pass, 
        		'us.da_xoa' => 0])->get();
		return $result;
	}

	public function getInfoByEmail($email) {
		$result = DB::table('users')->select('id as user_id','ten', 'email', 'sdt' , 'gioi_tinh', 'fb_id', 'diem_tich' , 'ngay_sinh', 'password' , 'avatar')->where('email', '=', $email)->get();
		return $result;
	}

	public function loginsdt($user) {
		$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'password', 'gioi_tinh' ,'sdt', 'diem_tich' , 'ngay_sinh' , 'dia_chi' , 'fb_id' , 'avatar' , 'id_vai_tro', 'quyen_he_thong' )
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
		$result = DB::table('DonHang as dh')->select('dh.ma_don_hang', 'ma_khuyen_mai', 'ngay_lap', 'phi_ship', 'tong_tien', 'ghi_chu');
			// ->leftjoin('ChiTietTrangThaiDonHang as ctttdh', 'ctttdh.ma_don_hang', '=', 'dh.ma_don_hang')
			// ->leftjoin('TrangThaiDonHang as ttdh', 'ttdh.ma_trang_thai', '=', 'ctttdh.trang_thai');
		if ($id_KH != null && $id_KH != '') {
			$result->where('ma_khach_hang', '=', $id_KH);
		}
		return $result->get();
	}

	public function getDetail($ma_don_hang) {
		$result = DB::table('ChiTietDonHang as ctdh')->select('ma_chi_tiet', 'ma_san_pham', 'so_luong', 'don_gia', 'kich_co', 'gia_khuyen_mai', 'thanh_tien', 'ghi_chu')
			->where('ctdh.ma_don_hang', '=', $ma_don_hang);
		return $result->get();
	}

	public function getStatusOrder($ma_don_hang) {
		$result = DB::table('ChiTietTrangThaiDonHang as ctttdh')->select('ten_trang_thai', 'thoi_gian')
			->leftjoin('TrangThaiDonHang as ttdh', 'ttdh.ma_trang_thai', '=', 'ctttdh.trang_thai')
			->where('ctttdh.ma_don_hang', '=', $ma_don_hang);
		return $result->get();
	}

	public function getUser($id_KH) {
		$result = DB::table('users')->select('id as user_id', 'ten', 'sdt', 'dia_chi');
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
		$result = DB::table('users')->select('id as user_id', 'ten', 'email', 'sdt' , 'gioi_tinh', 'fb_id','ngay_sinh', 'password')->where('fb_id', '=', $id_fb)->get();
		return $result;
	}

	public function loginfb($id_fb) {
		
			$result = DB::table('users as us')->select('us.ten', 'us.id as user_id', 'email', 'password', 'id_vai_tro', 'quyen_he_thong', 'fb_id')
	        ->leftjoin('PhanQuyen as per', 'per.tai_khoan', '=', 'us.id')
	        ->leftjoin('quyen as pe', 'pe.ma_so', '=', 'per.quyen_cho_phep')
	        ->where([
	        		'fb_id' => $id_fb, 
	        		'us.da_xoa' => 0])->get();
			return $result;
	}

	public function create($id_fb, $email) {
		if ($email != null && $email != '') {
			$result = DB::table('users')->insert([
						   'email' => $email,
				           'fb_id' => $id_fb,
				           'da_xoa' => 0,
       			 ]);
		}else {
			$result = DB::table('users')->insert([
				           'fb_id' => $id_fb,
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

	public function insertCart($id_KH, $ma_sp, $so_luong, $size, $note) {
		$result = DB::table('GioHang')->insert([
           'ma_khach_hang' => $id_KH,
           'ma_san_pham' => $ma_sp,
           'kich_co' => $size,
           'so_luong' => $so_luong,
           'ghi_chu' => $note
        ]);
        return $result;	
	}

	public function deleteCart($id_GH) {
		$result = DB::table('GioHang')->where('ma_gio_hang' , '=', $id_GH)->delete();
        return $result;	
	}

	public function deleteCartCustomer($id_KH) {
		$result = DB::table('GioHang')->where('ma_khach_hang' , '=', $id_KH)->delete();
        return $result;	
	}

	public function getQuantity($id_GH) {
		$result = DB::table('GioHang')->select('so_luong')->where('ma_gio_hang' , '=', $id_GH)->get();
        return $result;	
	}
	
	public function updateCart($ma_gio_hang, $ma_topping, $ten_topping, $so_luong_topping) {
		$result1 = DB::table('ChiTietDonHang')->where('ma_gio_hang', '=', $ma_gio_hang)->delete();
		$result = DB::table('ChiTietDonHang')->insert([
           'ma_san_pham' => $ma_topping,
           'ma_gio_hang' => $ChiTietDonHang,
           'so_luong' => $so_luong_topping,
        ]);
        return $result;	
	}
	
	public function getCartOfCustomer($id_KH) {
		$result = DB::table('GioHang')->select('ma_gio_hang', 'ma_san_pham', 'gia_san_pham', 'gia_vua', 'gia_lon', 'loai_chinh', 'kich_co', 'so_luong', 'ghi_chu')->join('SanPham', 'ma_so', '=', 'ma_san_pham')->join('LoaiSanPham', 'ma_loai_sp', '=', 'loai_sp')->where('ma_khach_hang' , '=', $id_KH)->get();
        return $result;
	}

	public function getInfoCustomer($id_KH) {
		$result = DB::table('users')->select('id', 'ten', 'sdt', 'dia_chi')->where('id' , '=', $id_KH)->get();
        return $result;
	}

	public function getEvaluate() {
		$result = DB::table('DanhGia')->select('ma_tk', 'ma_sp', 'so_diem', 'tieu_de', 'noi_dung', 'thoi_gian', 'hinh_anh', 'parent_id', 'duyet')->where('da_xoa' , '=', 0)->get();
        return $result;
	}

	public function getChildEvaluate($id_SP, $id_Evaluate) {
		$result = DB::table('DanhGia')->select('ma_danh_gia', 'ma_tk', 'ma_sp', 'so_diem', 'tieu_de', 'noi_dung', 'thoi_gian', 'hinh_anh', 'parent_id', 'duyet')->where(['da_xoa' => 0, 'ma_sp' => $id_SP, 'parent_id' => $id_Evaluate])->get();
        return $result;
	}

	public function getEvaluateOfProduct($id_SP) {
		$result = DB::table('DanhGia')->select('ma_danh_gia', 'ma_tk','so_diem', 'tieu_de', 'noi_dung', 'thoi_gian', 'hinh_anh', 'parent_id', 'duyet')->where(['da_xoa' => 0, 'ma_sp' => $id_SP])->get();
        return $result;
	}

	public function getBranch() {
		$result = DB::table('ChiNhanh')->join('KhuVuc', 'ChiNhanh.ma_khu_vuc', '=', 'KhuVuc.ma_khu_vuc')->select('ma_chi_nhanh','ten', 'dia_chi', 'latitude', 'longtitude', 'ten_khu_vuc','ngay_khai_truong', 'gio_mo_cua', 'gio_dong_cua')->where(['KhuVuc.da_xoa' => 0])->get();
        return $result;
	}

	public function addEvaluate($id_tk, $id_sp, $so_diem, $tieu_de, $noi_dung, $thoi_gian, $hinh_anh, $parent_id) {
		$result = DB::table('DanhGia')->insert([
           'ma_tk' => $id_tk,
           'ma_sp' => $id_sp,
           'so_diem' => $so_diem,
           'tieu_de' => $tieu_de,
           'noi_dung' => $noi_dung,
           'thoi_gian' => $thoi_gian,
           'hinh_anh' => $hinh_anh,
           'parent_id' => $parent_id,
           'duyet' => 1,
           'da_xoa' => 0,
        ]);
        return $result;	
	}

	public function addThanks($id_Evaluate, $id_KH) {
		$result = DB::table('CamOnDanhGia')->insert([
           'ma_danh_gia' => $id_Evaluate,
           'ma_kh' => $id_KH,
        ]);
        return $result;	
	}

	public function insertTopping($topping) {
		for ($i=0; $i < count($topping); $i++) { 
			$result = DB::table('ChiTietGiohang')->insert([
           		'ma_san_pham' => $topping[$i][0],
           		'so_luong' => $topping[$i][3]
        	]);	
		}
        return $result;	
	}

	public function getToppingCart($ma_gio_hang) {
		$result = DB::table('ChiTietGiohang')->select('ma_gio_hang', 'ma_san_pham', 'so_luong')->where(['ma_gio_hang' => $ma_gio_hang])->get();
        return $result;
	}

	public function getTopping($ma_sp) {
		$result = DB::table('ChiTietThucUong')->select('so_thu_tu', 'ma_chi_tiet', 'ma_san_pham', 'don_gia', 'so_luong')->where(['ma_chi_tiet' => $ma_sp])->get();
        return $result;
	}

	public function selectMaxId() {
		$result = DB::table('GioHang')->max('ma_gio_hang');
		return $result;
	}

	public function getCart($id_KH) {
		$result = DB::table('GioHang')->select('ma_gio_hang', 'ma_khach_hang', 'ma_san_pham', 'kich_co')->where('ma_khach_hang', '=', $id_KH)->get();
        return $result;
	}

	public function getDetailCart($ma_gio_hang) {
		$result = DB::table('ChiTietGiohang')->select('ma_san_pham', 'so_luong')->where('ma_gio_hang', '=', $ma_gio_hang)->get();
        return $result;
	}
}