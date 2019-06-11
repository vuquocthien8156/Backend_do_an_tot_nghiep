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

	public function search($name, $phone, $page=1, $gender) {
		$result = DB::table('users as us')->select('us.id', 'us.ten','us.sdt','us.ngay_sinh','us.gioi_tinh','us.diem_tich','us.email','us.da_xoa','us.avatar', 'us.dia_chi');
        
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
		return $result->orderBy('us.id', 'asc')->paginate(15);
	}

	public function delete($id) {
		$result = DB::table('users as us')->where('id','=',$id)->update(['da_xoa' => 1]);
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