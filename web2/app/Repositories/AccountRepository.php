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

	public function search($name, $page = 1, $pageSize = 15) {
		$result = DB::table('users as us')->select('us.id', 'us.ten','us.sdt','us.ngay_sinh','us.gioi_tinh','us.diem_tich','us.ten','us.email','us.trang_thai')->orderBy('us.id', 'asc')->paginate(15);
        if ($name != '' && $name != null) {
        	$result = DB::table('users as us')->select('us.id', 'us.ten','us.sdt','us.ngay_sinh','us.gioi_tinh','us.diem_tich','us.ten','us.email','us.trang_thai')
        ->where('us.ten','like', '%' .$name.'%')->orderBy('us.id', 'asc')->paginate(15);
        return $result;
        }
		return $result;

	}

	public function delete($id) {
		$result = DB::table('users as us')->where('id','=',$id)->update(['trang_thai' => -1]);
		return $result;

	}

}