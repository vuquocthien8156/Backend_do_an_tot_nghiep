<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use App\Models\quyen;
use App\Models\vaitro;
use App\Models\phanquyen;;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderRepository {

	public function listOrder($code) {
		$result = DB::table('DonHang')->select('DonHang.ma_don_hang as madh', 'ma_chu', 'thong_tin_giao_hang', 'ma_khach_hang', 'khuyen_mai','phi_ship', 'tong_tien', 'ghi_chu', 'ngay_lap', 'phuong_thuc_thanh_toan', 'DonHang.da_xoa', 'ten_khuyen_mai', 'ctttdh.trang_thai', 'ten_trang_thai')
		->leftjoin('KhuyenMai', 'ma_khuyen_mai', '=', 'khuyen_mai')
		->leftjoin('ChiTietTrangThaiDonHang as ctttdh', 'ctttdh.ma_don_hang', '=', 'DonHang.ma_don_hang')
		->leftjoin('TrangThaiDonHang', 'ma_trang_thai', '=', 'ctttdh.trang_thai');
		if ($code != null && $code != '') {
		 	$result->where(function($where) use ($code) {
                $where->whereRaw('lower(ma_chu) like ? ', ['%' . trim(mb_strtolower($code, 'UTF-8')) . '%']);
         
            });
		} 
		return $result->orderBy('DonHang.ma_don_hang', 'desc')->paginate(15);
	}

	public function updateStatus($id, $status) {
		$result = DB::table('ChiTietTrangThaiDonHang')->where('ma_don_hang','=',$id)->update(['trang_thai' => $status]);
		return $result;
	}

	public function getPoint($ma_kh){
		$result = DB::table('users')->select('diem_tich')->where([
			'da_xoa' => 0,
			'id' => $ma_kh
		]);
		return $result->get();
	}

	public function addpoint($ma_kh, $totalPoint) {
		$result = DB::table('users')->where('id', '=', $ma_kh)
		->update([
			'diem_tich' => $totalPoint
		]);
		return $result;
	}

	public function delete($id) {
		$result = DB::table('DonHang')->where('ma_don_hang','=',$id)->update(['da_xoa' => 1]);
		return $result;
	}
}