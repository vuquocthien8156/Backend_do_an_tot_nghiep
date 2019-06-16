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

	public function searchProduct($name, $page, $ma_loai, $mo_ta, $masp) {
        $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.so_lan_dat', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta' )
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

        if ($masp != '' && $masp != null) {
            $result->where(function($where) use ($masp) {
                $where->whereRaw('lower(sp.ma_chu) like ? ', ['%' . trim(mb_strtolower($masp, 'UTF-8')) . '%']);
         
            });
        }

		return $result->orderBy('sp.ma_so', 'asc')->paginate(15);
    }

    public function searchProductAPI($name, $page, $ma_loai, $mo_ta , $ma_loai_chinh) {
       if ($page == null) {
            $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta',  'lsp.loai_chinh')
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

        if($ma_loai_chinh != '' && $ma_loai_chinh != null){
            $result->where('lsp.loai_chinh', '=', $ma_loai_chinh);
        }

        if ($mo_ta != '' && $mo_ta != null) {
            $result->where(function($where) use ($mo_ta) {
                $where->whereRaw('lower(sp.mo_ta) like ? ', ['%' . trim(mb_strtolower($mo_ta, 'UTF-8')) . '%']);
            });
        }

        return $result->orderBy('sp.ma_so', 'asc')->get();
        }else {
            $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta')
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
        $now = Carbon::now();
        $from_date = $now->subDay(7)->toDateString();
        $to_date = $now->addDay(7)->toDateString();
        $result = DB::table('ChiTietDonHang as ctdh')->select('ctdh.ma_san_pham' ,DB::raw('count(ctdh.ma_san_pham) as total'))
                ->join('DonHang as dh','dh.ma_don_hang','=','ctdh.ma_don_hang')
                ->groupBy('ctdh.ma_san_pham')  
                ->orderBy('total','desc')
                ->skip(0)->take(10);
        $result = $result->where('ngay_lap', '<=', $to_date);
        $result = $result->where('ngay_lap', '>=', $from_date);
        return $result->get();
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
        $result = DB::table('SanPhamYeuThich')->select('ma_san_pham' , DB::raw('count(ma_san_pham) as total'))
                ->where('thich', '=', 1)
                ->groupBy('ma_san_pham')  
                 ->orderBy('total','desc')
                 ->skip(0)->take(10)
                 ->get();
        return $result;
    }

    public function getlist($id) {
       $result = DB::table('SanPham')->select('ma_so', 'ma_chu','ten', 'gia_san_pham', 'gia_vua', 'gia_lon' , 'hinh_san_pham')->where('ma_so','=', $id);
       return $result->get();
    }

    public function searchProductById($id) {
        $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.so_lan_dat', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta')
        ->leftjoin('LoaiSanPham as lsp', 'lsp.ma_loai_sp', '=', 'sp.loai_sp')
        ->where([
            'sp.ma_so' => $id,
        ]);
        return $result->get();
    }

    public function searchNews($id) {
        $result = DB::table('TinTuc')->select('ten_tin_tuc', 'noi_dung', 'hinh_tin_tuc')
        ->where([
            'ma_tin_tuc' => $id,
        ]);
        return $result->get();
    }

    public function searchKM($id) {
        $result = DB::table('KhuyenMai')->select('hinh_anh', 'ten_khuyen_mai', 'mo_ta', 'so_phan_tram')
        ->where([
            'ma_khuyen_mai' => $id,
        ]);
        return $result->get();
    }

    public function forWeek($dayStart, $dayEnd) {
        $result = DB::table('ChiTietDonHang as ctdh')->select('ctdh.ma_san_pham',DB::raw('count(ctdh.ma_san_pham) as total'))
                ->join('DonHang as dh','dh.ma_don_hang','=','ctdh.ma_don_hang')
                ->groupBy('ctdh.ma_san_pham')  
                ->orderBy('total','desc')
                ->skip(0)->take(10);
        $result = $result->where('ngay_lap', '>=', $dayStart);
        $result = $result->where('ngay_lap', '<=', $dayEnd);
        return $result->get();
    }

    public function searchProductTK($masp) {
        $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.so_lan_dat', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta' )
        ->leftjoin('LoaiSanPham as lsp', 'lsp.ma_loai_sp', '=', 'sp.loai_sp')
        ->where([
            'lsp.da_xoa' => 0,
        ]);

        if ($masp != '' && $masp != null) {
            $result->where(function($where) use ($masp) {
                $where->whereRaw('lower(sp.ma_chu) like ? ', ['%' . trim(mb_strtolower($masp, 'UTF-8')) . '%']);
         
            });
        }
        return $result->orderBy('sp.ma_so', 'asc')->get();
    }

    public function getIdMax() {
        $result = DB::table('SanPham')->max('ma_so');
        return $result;
    }

    public function inserImage($avatar_path, $getIdMax) {
         $result = DB::table('HinhAnh')->insert([
            'object_id' => $getIdMax,
            'kieu' => 1,
            'url' => $avatar_path,
            'da_xoa' => 0,
        ]);
        return $result;
    }
}