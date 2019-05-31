<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use App\Models\quyen;
use App\Models\vaitro;
use App\Models\phanquyen;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BranchRepository {
    public function listPlace() {
       $result = DB::table('KhuVuc')->select('ma_khu_vuc', 'ten_khu_vuc')->where('da_xoa' , '=', 0);
       return $result->get();
    }

    public function listBranch($name, $place){
        $result = DB::table('ChiNhanh')->select('ma_chi_nhanh', 'ten', 'dia_chi', 'latitude', 'longitude', 'ngay_khai_truong', 'gio_mo_cua', 'gio_dong_cua', 'ten_khu_vuc', 'sdt', 'ChiNhanh.da_xoa')->join('KhuVuc' , 'KhuVuc.ma_khu_vuc','=' , 'ChiNhanh.ma_khu_vuc')->where('ChiNhanh.da_xoa' , '=', 0);
        if ($name != '' && $name != null) {
            $result->where(function($where) use ($name) {
                $where->whereRaw('lower(ten) like ? ', ['%' . trim(mb_strtolower($name, 'UTF-8')) . '%']);
         
            });
        }
        if ($place) {
             $result->where('KhuVuc.ma_khu_vuc', '=', $place);
        }
       return $result->orderBy('ma_chi_nhanh', 'desc')->paginate(15);   
    }
}