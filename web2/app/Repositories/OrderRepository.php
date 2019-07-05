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

	public function listOrder($id, $code) {
		$result = DB::table('DonHang')->select('DonHang.ma_don_hang as madh', 'ma_chu', 'thong_tin_giao_hang', 'ma_khach_hang', 'khuyen_mai','phi_ship', 'tong_tien', 'DonHang.ghi_chu', 'ngay_lap', 'phuong_thuc_thanh_toan', 'DonHang.da_xoa', 'ten_khuyen_mai', 'so_diem', 'gia_khuyen_mai')->where('DonHang.da_xoa', '=', 0)
		->join('KhuyenMai', 'ma_khuyen_mai', '=', 'khuyen_mai')
		->join('ChiTietDonHang', 'ChiTietDonHang.ma_don_hang','=','DonHang.ma_don_hang')
		->join('LichSuDiem', 'LichSuDiem.ma_don_hang', '=','DonHang.ma_don_hang');
		// ->leftjoin('ChiTietTrangThaiDonHang as ctttdh', 'ctttdh.ma_don_hang', '=', 'DonHang.ma_don_hang')
		// ->leftjoin('TrangThaiDonHang', 'ma_trang_thai', '=', 'ctttdh.trang_thai');
		if ($code != null && $code != '') {
		 	$result->where(function($where) use ($code) {
                $where->whereRaw('lower(ma_chu) like ? ', ['%' . trim(mb_strtolower($code, 'UTF-8')) . '%']);
         
            });
		}
		if ($id != null && $id != '') {
		 	$result->where('DonHang.ma_don_hang','=',$id);
		 } 
		return $result->distinct()->orderBy('DonHang.ma_don_hang', 'desc')->paginate(15);
	}

	public function statusOrder($id) {
		$result = DB::table('ChiTietTrangThaiDonHang')
		->where('ma_don_hang','=',$id)->max('trang_thai');
		return $result;
	}

	public function getNameStatus($id) {
		$result = DB::table('TrangThaiDonHang')
		->select('ten_trang_thai', 'ma_trang_thai')
		->where('ma_trang_thai','=',$id);
		return $result->get();
	}

	public function getStatus() {
		$result = DB::table('TrangThaiDonHang')->select();
		return $result->orderBy('ma_trang_thai', 'asc')->get();
	}

	public function getAllDisCount() {
		$result = DB::table('KhuyenMai')->select('ma_khuyen_mai', 'ten_khuyen_mai')->where('da_xoa','=',0)->get();
		return $result;
	}

	public function updateStatus($id, $status) {
		// $result = DB::table('ChiTietTrangThaiDonHang')->where('ma_don_hang','=',$id)->update(['trang_thai' => $status]);
		// return $result;
		$now = Carbon::now();
		$result = DB::table('ChiTietTrangThaiDonHang')->insert([
           'ma_don_hang' => $id,
           'trang_thai' => $status,
           'thoi_gian' => $now,
           'da_xoa' => 0,
        ]) ;
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

	public function editOrder($thong_tin_giao_hang, $ten_khuyen_mai, $phi_ship, $ngay_lap, $tong_tien, $ghi_chu, $phuong_thuc_thanh_toan, $id) {
		$result = DB::table('DonHang')->where('ma_don_hang','=',$id)
		->update([
			'thong_tin_giao_hang' => $thong_tin_giao_hang,
			'khuyen_mai' => $ten_khuyen_mai,
			'phi_ship' => $phi_ship,
			'ngay_lap' => $ngay_lap,
			'tong_tien' => $tong_tien,
			'ghi_chu' => $ghi_chu,
			'phuong_thuc_thanh_toan' => $phuong_thuc_thanh_toan,
		]);
		return $result;
	}

	public function detailOrder($id) {
		$result = DB::table('ChiTietDonHang')->select('so_luong', 'don_gia', 'kich_co', 'gia_khuyen_mai', 'thanh_tien', 'ghi_chu', 'ten')->where('ma_don_hang','=',$id)
		->leftjoin('SanPham', 'ma_so', '=', 'ma_san_pham')->get();
		return $result;
	}
}