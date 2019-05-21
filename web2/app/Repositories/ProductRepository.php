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

class ProductRepository {
	public function searchProduct($name, $page, $ma_loai, $mo_ta) {
        $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.so_lan_dat', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta')
        ->leftjoin('LoaiSanPham as lsp', 'lsp.ma_loai_sp', '=', 'sp.loai_sp')
        ->where([
			'lsp.da_xoa' => 0,
		]);

		if ($name != '' && $name != null) {
            $result->where(function($where) use ($name) {
                $where->whereRaw('lower(sp.ten) like ? ', ['%' . trim(mb_strtolower($name, 'UTF-8')) . '%']);
         
            });
        }

        if ($ma_loai != '' && $ma_loai != null) {
             $result->where('sp.loai_sp', '=', $ma_loai);
        }

        if ($mo_ta != '' && $mo_ta != null) {
            $result->where(function($where) use ($mo_ta) {
                $where->whereRaw('lower(sp.mo_ta) like ? ', ['%' . trim(mb_strtolower($mo_ta, 'UTF-8')) . '%']);
         
            });
        }

		return $result->orderBy('sp.ma_so', 'asc')->paginate(15);
    }

    public function searchProductAPI($name, $page, $ma_loai, $mo_ta) {
       if ($page == null) {
            $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.so_lan_dat', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta')
        ->leftjoin('LoaiSanPham as lsp', 'lsp.ma_loai_sp', '=', 'sp.loai_sp')
        ->where([
            'lsp.da_xoa' => 0,
        ]);

        if ($name != '' && $name != null) {
            $result->where(function($where) use ($name) {
                $where->whereRaw('lower(sp.ten) like ? ', ['%' . trim(mb_strtolower($name, 'UTF-8')) . '%']);
         
            });
        }

        if ($ma_loai != '' && $ma_loai != null) {
             $result->where('sp.loai_sp', '=', $ma_loai);
        }

        if ($mo_ta != '' && $mo_ta != null) {
            $result->where(function($where) use ($mo_ta) {
                $where->whereRaw('lower(sp.mo_ta) like ? ', ['%' . trim(mb_strtolower($mo_ta, 'UTF-8')) . '%']);
         
            });
        }

        return $result->orderBy('sp.ma_so', 'asc')->get();
        }else {
            $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.so_lan_dat', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta')
        ->leftjoin('LoaiSanPham as lsp', 'lsp.ma_loai_sp', '=', 'sp.loai_sp')
        ->where([
            'lsp.da_xoa' =>0,
        ]);

        if ($name != '' && $name != null) {
            $result->where(function($where) use ($name) {
                $where->whereRaw('lower(sp.ten) like ? ', ['%' . trim(mb_strtolower($name, 'UTF-8')) . '%']);
         
            });
        }

        if ($ma_loai != '' && $ma_loai != null) {
             $result->where('sp.loai_sp', '=', $ma_loai);
        }

        if ($mo_ta != '' && $mo_ta != null) {
            $result->where(function($where) use ($mo_ta) {
                $where->whereRaw('lower(sp.mo_ta) like ? ', ['%' . trim(mb_strtolower($mo_ta, 'UTF-8')) . '%']);
         
            });
        }

        return $result->orderBy('sp.ma_so', 'asc')->paginate(15);
        }
    }

    public function searchRankProduct() {
        $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.so_lan_dat', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta')
        ->leftjoin('LoaiSanPham as lsp', 'lsp.ma_loai_sp', '=', 'sp.loai_sp')
        ->where([
            'lsp.da_xoa' => 0,
        ]);
        return $result->orderBy('sp.so_lan_dat', 'asc')->limit(10)->get();
    }

    public function delete($id) {
		$result = DB::table('SanPham')->where('ma_so','=',$id)->update(['daxoa' => 1]);
		return $result;

	}

    public function loaisp() {
        $result = DB::table('LoaiSanPham')->select('ma_loai_sp', 'ten_loai_sp')
        ->where(['da_xoa' => 0])->orderBy('ma_loai_sp', 'asc')->get();
        return $result;
    }

    public function addProduct($avatar_path, $ten, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta) {
        $result = DB::table('SanPham')->insert([
            'ma_chu' => $ma,
            'ten' => $ten,
            'gia_san_pham' => $gia_goc,
            'gia_vua' => $gia_size_vua,
            'gia_lon' => $gia_size_lon,
            'loai_sp' => $loaisp,
            'ngay_ra_mat' => $ngay_ra_mat,
            'hinh_san_pham' => $avatar_path,
            'mo_ta' => $mo_ta,
            'daxoa' => 0
        ]);
        return $result;
    }

    public function editProduct($avatar_path, $ten, $id, $so_lan_order, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta) {
        $result = DB::table('SanPham')->where('ma_so','=',$id)->update([
            'ma_chu' => $ma,
            'ten' => $ten,
            'gia_san_pham' => $gia_goc,
            'gia_vua' => $gia_size_vua,
            'gia_lon' => $gia_size_lon,
            'loai_sp' => $loaisp,
            'ngay_ra_mat' => $ngay_ra_mat,
            'hinh_san_pham' => $avatar_path,
            'mo_ta' => $mo_ta,
            'so_lan_dat' => $so_lan_order,
        ]);
        return $result;

    }

    public function getIdSp() {
        $result = DB::table('SanPhamYeuThich')->select('ma_san_pham')->where('thich' , '=', 1)->DISTINCT();
        return $result->orderBy('ma_san_pham', 'asc')->get();
    }

    public function getAmount($id) {
       $result = DB::table('SanPhamYeuThich')->select('ma_san_pham')->where(['ma_san_pham' => $id, 'thich' => 1]);
       return $result->get();
    }

    public function getlist($id) {
       $result = DB::table('SanPham')->select('ma_so', 'ma_chu','ten', 'gia_san_pham', 'gia_vua', 'gia_lon' , 'hinh_san_pham')->where('ma_so', '=', $id);
       return $result->get();
    }
}