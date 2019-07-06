<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use App\Models\quyen;
use App\Models\vaitro;
use App\Models\phanquyen;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BranchRepository {
    public function listPlace() {
       $result = DB::table('KhuVuc')->select('ma_khu_vuc', 'ten_khu_vuc')->where('da_xoa' , '=', 0);
       return $result->get();
    }

    public function listBranch($page){
        $result = DB::table('ChiNhanh')->select('ma_chi_nhanh', 'ChiNhanh.ma_khu_vuc','ten', 'ten_khu_vuc', 'dia_chi', 'latitude', 'longitude', 'ngay_khai_truong', 'gio_mo_cua', 'gio_dong_cua','so_dien_thoai', 'ChiNhanh.da_xoa')->leftjoin('KhuVuc', 'KhuVuc.ma_khu_vuc', '=','ChiNhanh.ma_khu_vuc')->where('ChiNhanh.da_xoa','=', 0);
        // if ($name != '' && $name != null) {
        //     $result->where(function($where) use ($name) {
        //         $where->whereRaw('lower(ten) like ? ', ['%' . trim(mb_strtolower($name, 'UTF-8')) . '%']);
         
        //     });
        // }
        // if ($place) {
        //      $result->where('KhuVuc.ma_khu_vuc', '=', $place);
        // }
       return $result->orderBy('ma_chi_nhanh', 'desc')->paginate(4);   
    }

    public function saveBranch($name, $latitude, $longitude, $phone_branch, $address, $id_kv) {
        $result = DB::table('ChiNhanh')
        ->insert([
            'ten' => $name,
            'dia_chi' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'sdt' =>$phone_branch,
            'ma_khu_vuc' => $id_kv,
            'da_xoa' => 0
        ]);
        return $result;
    }

    public function deleteBranch($id,$status) {
         $result = DB::table('ChiNhanh')->where('ma_chi_nhanh' , '=', $id)
        ->update([
            'da_xoa' => $status
        ]);
        return $result;
    }

    public function updateBranch($id_branch_update, $address_update, $phone_branch_update, $name_branch_update, $latitude_update, $longitude_update, $id_kv) {
         $result = DB::table('ChiNhanh')->where('ma_chi_nhanh' , '=', $id_branch_update)
        ->update([
            'ten' => $name_branch_update,
            'dia_chi' => $address_update,
            'latitude' => $latitude_update,
            'longitude' => $longitude_update,
            'sdt' =>$phone_branch_update,
            'ma_khu_vuc' => $id_kv,
        ]);
        return $result;
    }
}