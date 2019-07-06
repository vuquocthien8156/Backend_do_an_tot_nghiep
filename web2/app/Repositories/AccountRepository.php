<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use App\Models\quyen;
use App\Models\phanquyen;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AccountRepository {

	public function search($name, $phone, $page=1, $gender, $loai_tai_khoan) {
		$result = DB::table('users as us')->select('us.id', 'us.ten','us.sdt','us.ngay_sinh','us.gioi_tinh','us.diem_tich','us.email','us.da_xoa','us.avatar', 'us.dia_chi', 'ten_loai_tai_khoan')
		->join('LoaiTaiKhoan', 'ma_loai_tai_khoan', '=', 'loai_tai_khoan');
        
        if ($name != '' && $name != null) {
            $result->where(function($where) use ($name) {
                $where->whereRaw('lower(us.ten) like ? ', ['%' . trim(mb_strtolower($name, 'UTF-8')) . '%']);
         
            });
        }
        if ($phone != '' && $phone != null) {
            $result->where(function($where) use ($phone) {
                $where->whereRaw('us.sdt like ? ', ['%' . $phone . '%']);
         
            });
        }
        if ($gender != '' && $gender != null) {
        	$result->where('us.gioi_tinh', '=', $gender);
        }
        if($loai_tai_khoan != null && $loai_tai_khoan != "") {
        	$result->where('us.loai_tai_khoan', '=', $loai_tai_khoan);	
        }
		return $result->orderBy('us.id', 'asc')->paginate(15);
	}

	public function loaiTaiKhoan() {
		$result = DB::table('LoaiTaiKhoan')->select('ma_loai_tai_khoan', 'ten_loai_tai_khoan')->where('da_xoa', '=', 0);
		return $result->orderBy('ma_loai_tai_khoan')->get();
	}

	public function delete($id, $status) {
		$result = DB::table('users as us')->where('id','=',$id)->update(['da_xoa' => $status]);
		return $result;

	}

	public function editUser($avatar_path, $ten, $id, $SDT, $NS, $gender, $diemtich, $diachi, $email, $now) {
		$result = DB::table('users as us')->where('id','=',$id)
		->update([
			'avatar' => $avatar_path,
			'ten' => $ten,
			'sdt' => $SDT,
			'gioi_tinh' => $gender,
			'ngay_sinh' => $NS,
			'diem_tich' => $diemtich,
			'dia_chi' => $diachi,
			'email' => $email,
			'updated_at' => $now,
		]);
		return $result;
	}

}