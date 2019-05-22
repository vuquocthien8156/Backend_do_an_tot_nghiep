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
		$result = DB::table('users as us')->select('us.id', 'us.ten','us.sdt','us.ngay_sinh','us.gioi_tinh','us.diem_tich','us.email','us.da_xoa','us.avatar', 'us.dia_chi');
        
        if ($name != '' && $name != null) {
            $result->where(function($where) use ($name) {
                $where->whereRaw('lower(us.ten) like ? ', ['%' . trim(mb_strtolower($name, 'UTF-8')) . '%']);
         
            });
        }
		return $result->orderBy('us.id', 'asc')->paginate(15);
	}

	public function delete($id) {
		$result = DB::table('users as us')->where('id','=',$id)->update(['da_xoa' => -1]);
		return $result;

	}

}