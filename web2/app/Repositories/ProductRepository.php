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
        $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta' )
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
            $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta' ,  'lsp.loai_chinh')
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

    public function deleteDiscount($id) {
        $result = DB::table('KhuyenMai')->where('ma_khuyen_mai','=',$id)->update(['da_xoa' => 1]);
        return $result;
    }

    public function deleteNews($id) {
        $result = DB::table('TinTuc')->where('ma_tin_tuc','=',$id)->update(['da_xoa' => 1]);
        return $result;
    }

    public function loaisp() {
        $result = DB::table('LoaiSanPham')->select('ma_loai_sp', 'ten_loai_sp')
        ->where(['da_xoa' => 0])->orderBy('ma_loai_sp', 'asc')->get();
        return $result;
    }

    public function sanPham() {
        $result = DB::table('SanPham')->select('ma_so', 'ten')
        ->where(['daxoa' => 0])->orderBy('ma_so', 'asc')->get();
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

    public function addNews($ten, $ND, $ngay_tao, $avatar_path, $NĐ) {
        $result = DB::table('TinTuc')->insert([
            'ten_tin_tuc' => $ten,
            'noi_dung' => $ND,
            'hinh_tin_tuc' => $avatar_path,
            'da_xoa' => 0,
            'ngay_dang' => $NĐ,
            'ngay_tao' => $ngay_tao
        ]);
        return $result;
    }

    public function addDiscount($avatar_path,$now,$type,$ma, $ten,$MT, $SPT, $ST, $SSPQD, $STQDTT, $NBD ,$NKT, $GHSC,$SSPTK,$SP ) {
        if ($type == 1) {
            $result = DB::table('KhuyenMai')->insert([
             'ma_code' => $ma,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten,
                'mo_ta' => $MT,
                'so_phan_tram' => $SPT,
                'so_tien' => 0,
                'so_sp_qui_dinh' => $SSPQD,
                'so_tien_qui_dinh_toi_thieu' => 0,
                'ma_san_pham' => 0,
                'ngay_bat_dau' => $NBD,
                'ngay_ket_thuc' => $NKT,
                'gioi_han_so_code' => $GHSC,
                'so_sp_tang_kem' => 0,
                'da_xoa' => 0,
                'hien_slider' => 0,
        ]);
        }
        if ($type == 2) {
            $result = DB::table('KhuyenMai')->insert([
           'ma_code' => $ma,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten,
                'mo_ta' => $MT,
                'so_phan_tram' => $SPT,
                'so_tien' => $ST,
                'so_sp_qui_dinh' => $SSPQD,
                'so_tien_qui_dinh_toi_thieu' => $STQDTT,
                'ma_san_pham' => 0,
                'ngay_bat_dau' => $NBD,
                'ngay_ket_thuc' => $NKT,
                'gioi_han_so_code' => $GHSC,
                'so_sp_tang_kem' => 0,
                'da_xoa' => 0,
                'hien_slider' => 0,
        ]);
        }
        if ($type == 3) {
            $result = DB::table('KhuyenMai')->insert([
            'ma_code' => $ma,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten,
                'mo_ta' => $MT,
                'so_phan_tram' => 0,
                'so_tien' => 0,
                'so_sp_qui_dinh' => 0,
                'so_tien_qui_dinh_toi_thieu' => $STQDTT,
                'ma_san_pham' => 0,
                'ngay_bat_dau' => $NBD,
                'ngay_ket_thuc' => $NKT,
                'gioi_han_so_code' => $GHSC,
                'so_sp_tang_kem' => $SSPTK,
                'da_xoa' => 0,
                'hien_slider' => 0,
        ]);
        }
        if ($type == 4) {
            $result = DB::table('KhuyenMai')->insert([
           'ma_code' => $ma,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten,
                'mo_ta' => $MT,
                'so_phan_tram' => 0,
                'so_tien' => $ST,
                'so_sp_qui_dinh' => $SSPQD,
                'so_tien_qui_dinh_toi_thieu' => $STQDTT,
                'ma_san_pham' => 0,
                'ngay_bat_dau' => $NBD,
                'ngay_ket_thuc' => $NKT,
                'gioi_han_so_code' => $gioi_han_so_code,
                'so_sp_tang_kem' => 0,
                'da_xoa' => 0,
                'hien_slider' => 0,
        ]);
        }
        if ($type == 5) {
            $result = DB::table('KhuyenMai')->insert([
             'ma_code' => $ma,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten,
                'mo_ta' => $MT,
                'so_phan_tram' => 0,
                'so_tien' => 0,
                'so_sp_qui_dinh' => 0,
                'so_tien_qui_dinh_toi_thieu' => $STQDTT,
                'ma_san_pham' => $SP,
                'ngay_bat_dau' => $NBD,
                'ngay_ket_thuc' => $NKT,
                'gioi_han_so_code' => $GHSC,
                'so_sp_tang_kem' => $SSPTK,
                'da_xoa' => 0,
                'hien_slider' => 0,
        ]);
        }
        return $result;
    }

    public function editProduct($avatar_path, $ten, $id, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta) {
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
        ]);
        return $result;

    }

    public function editDiscount($ten_khuyen_mai, $id,$ma_code,$mo_ta,$so_phan_tram ,$so_tien ,$so_sp_qui_dinh ,$so_tien_qui_dinh_toi_thieu,$gioi_han_so_code ,$ngay_bat_dau ,$ngay_ket_thuc ,$id_now,$type, $so_sp_tang_kem, $avatar_path) {
        if ($type == 1) {
            $result = DB::table('KhuyenMai')->where('ma_khuyen_mai','=',$id)->update([
                'ma_code' => $ma_code,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten_khuyen_mai,
                'mo_ta' => $mo_ta,
                'so_phan_tram' => $so_phan_tram,
                'so_tien' => 0,
                'so_sp_qui_dinh' => $so_sp_qui_dinh,
                'so_tien_qui_dinh_toi_thieu' => 0,
                'ma_san_pham' => 0,
                'ngay_bat_dau' => $ngay_bat_dau,
                'ngay_ket_thuc' => $ngay_ket_thuc,
                'gioi_han_so_code' => $gioi_han_so_code,
                'so_sp_tang_kem' => 0,
            ]);
        }
        if ($type == 2) {
            $result = DB::table('KhuyenMai')->where('ma_khuyen_mai','=',$id)->update([
                'ma_code' => $ma_code,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten_khuyen_mai,
                'mo_ta' => $mo_ta,
                'so_phan_tram' => $so_phan_tram,
                'so_tien' => 0,
                'so_sp_qui_dinh' => $so_sp_qui_dinh,
                'so_tien_qui_dinh_toi_thieu' => 0,
                'ma_san_pham' => 0,
                'ngay_bat_dau' => $ngay_bat_dau,
                'ngay_ket_thuc' => $ngay_ket_thuc,
                'gioi_han_so_code' => $gioi_han_so_code,
                'so_sp_tang_kem' => 0,
            ]);
        }
        if ($type == 3) {
            $result = DB::table('KhuyenMai')->where('ma_khuyen_mai','=',$id)->update([
                'ma_code' => $ma_code,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten_khuyen_mai,
                'mo_ta' => $mo_ta,
                'so_phan_tram' => 0,
                'so_tien' => 0,
                'so_sp_qui_dinh' => 0,
                'so_tien_qui_dinh_toi_thieu' => $so_tien_qui_dinh_toi_thieu,
                'ma_san_pham' => 0,
                'ngay_bat_dau' => $ngay_bat_dau,
                'ngay_ket_thuc' => $ngay_ket_thuc,
                'gioi_han_so_code' => $gioi_han_so_code,
                'so_sp_tang_kem' => $so_sp_tang_kem,
            ]);
        }
        if ($type == 4) {
            $result = DB::table('KhuyenMai')->where('ma_khuyen_mai','=',$id)->update([
                'ma_code' => $ma_code,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten_khuyen_mai,
                'mo_ta' => $mo_ta,
                'so_phan_tram' => 0,
                'so_tien' => $so_tien,
                'so_sp_qui_dinh' => $so_sp_qui_dinh,
                'so_tien_qui_dinh_toi_thieu' => $so_tien_qui_dinh_toi_thieu,
                'ma_san_pham' => 0,
                'ngay_bat_dau' => $ngay_bat_dau,
                'ngay_ket_thuc' => $ngay_ket_thuc,
                'gioi_han_so_code' => $gioi_han_so_code,
                'so_sp_tang_kem' => 0,
            ]);
        }
        if ($type == 5) {
            $result = DB::table('KhuyenMai')->where('ma_khuyen_mai','=',$id)->update([
                'ma_code' => $ma_code,
                'hinh_anh' => $avatar_path,
                'ten_khuyen_mai' => $ten_khuyen_mai,
                'mo_ta' => $mo_ta,
                'so_phan_tram' => 0,
                'so_tien' => 0,
                'so_sp_qui_dinh' => 0,
                'so_tien_qui_dinh_toi_thieu' => $so_tien_qui_dinh_toi_thieu,
                'ma_san_pham' => $ma_san_pham,
                'ngay_bat_dau' => $ngay_bat_dau,
                'ngay_ket_thuc' => $ngay_ket_thuc,
                'gioi_han_so_code' => $gioi_han_so_code,
                'so_sp_tang_kem' => $so_sp_tang_kem,
            ]);
        }
        return $result;

    }

    public function editNews($avatar_path, $ten, $id, $ND, $date) {
        $result = DB::table('TinTuc')->where('ma_tin_tuc','=',$id)->update([
            'ten_tin_tuc' => $ten,
            'noi_dung' => $ND,
            'ngay_dang' => $date,
            'hinh_tin_tuc' => $avatar_path,
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
       $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta' ,  'lsp.loai_chinh')
        ->leftjoin('LoaiSanPham as lsp', 'lsp.ma_loai_sp', '=', 'sp.loai_sp')
        ->where([
            'sp.ma_so' => $id,
        ]);
       return $result->get();
    }

    public function searchProductById($id) {
        $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta')
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

    public function forTK($dayStart, $dayEnd) {
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
        $result = DB::table('SanPham as sp')->select('sp.ma_so', 'lsp.ma_loai_sp', 'sp.ma_chu', 'sp.ten','sp.gia_san_pham', 'sp.gia_vua', 'sp.gia_lon', 'sp.ngay_ra_mat', 'lsp.ten_loai_sp', 'sp.daxoa', 'sp.hinh_san_pham', 'sp.mo_ta' )
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

    public function getIdMaxNews() {
        $result = DB::table('TinTuc')->max('ma_tin_tuc');
        return $result;
    }

    public function getIdMaxDiscount() {
        $result = DB::table('KhuyenMai')->max('ma_khuyen_mai');
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

    public function inserImageDiscount($avatar_path, $getIdMax) {
         $result = DB::table('HinhAnh')->insert([
            'object_id' => $getIdMax,
            'kieu' => 4,
            'url' => $avatar_path,
            'da_xoa' => 0,
        ]);
        return $result;
    }

    public function inserImageNews($avatar_path, $getIdMax) {
         $result = DB::table('HinhAnh')->insert([
            'object_id' => $getIdMax,
            'kieu' => 2,
            'url' => $avatar_path,
            'da_xoa' => 0,
        ]);
        return $result;
    }

    public function deleteImg($ma_sp, $type) {
         $result = DB::table('HinhAnh')->where(['object_id' => $ma_sp, 'kieu' => $type])->update([
            'da_xoa' => 1,
        ]);
        return $result;
    }

    public function searchDiscount($type) {
            $result = DB::table('KhuyenMai')->select()->where(['da_xoa'=>0]);
            if ($type == 1) {
                 $result =  $result->where([['da_xoa', '=', 0], ['so_phan_tram', '!=', 0], ['so_tien', '=', 0], ['so_sp_qui_dinh', '!=', 0], ['so_tien_qui_dinh_toi_thieu', '=', 0]]);
            }
            if ($type == 2) {
                $result =  $result->where([['da_xoa', '=', 0], ['so_phan_tram', '!=', 0], ['so_tien', '!=', 0], ['so_sp_qui_dinh', '!=', 0], ['so_tien_qui_dinh_toi_thieu', '!=', 0]]);
            }
            if ($type == 4) {
               $result =  $result->where([['da_xoa', '=', 0], ['so_phan_tram', '=', 0], ['so_tien', '!=', 0], ['so_sp_qui_dinh', '!=', 0], ['so_tien_qui_dinh_toi_thieu', '!=', 0]]);
            }
            if ($type == 3) {
                $result =  $result->where([['da_xoa', '=', 0], ['so_phan_tram', '=', 0], ['so_tien', '=', 0], ['so_sp_qui_dinh', '!=', 0], ['so_tien_qui_dinh_toi_thieu', '=', 0], ['ma_san_pham', '=', 0]]);
            }
            if ($type == 5) {
                $result =  $result->where([['da_xoa', '=', 0], ['so_phan_tram', '=', 0], ['so_tien', '=', 0], ['so_sp_qui_dinh', '!=', 0], ['so_tien_qui_dinh_toi_thieu', '=', 0], ['ma_san_pham', '!=', 0]]);
            }
        return $result->orderBy('ma_khuyen_mai', 'desc')->paginate(15);
    }

    public function searchNews1($name) {
        $result = DB::table('TinTuc')->select()->where(['da_xoa'=>0]);
        if ($name != null && $name != '') {
            $result->where(function($where) use ($name) {
                $where->whereRaw('lower(ten_tin_tuc) like ? ', ['%' . trim(mb_strtolower($name, 'UTF-8')) . '%']);
         
            });
        }
        return $result->orderBy('ma_tin_tuc', 'desc')->paginate(15);
    }

    public function getImg($ma_sp, $type) {
        if ($type == 1) {
            $result = DB::table('HinhAnh')->select('url')->where(['da_xoa'=>0,'object_id'=>$ma_sp, 'kieu' => 1]);
        }
        if ($type == 4) {
            $result = DB::table('HinhAnh')->select('url')->where(['da_xoa'=>0,'object_id'=>$ma_sp, 'kieu' => 4]);
        }
        if ($type == 2) {
            $result = DB::table('HinhAnh')->select('url')->where(['da_xoa'=>0,'object_id'=>$ma_sp, 'kieu' => 2]);
        }
        return $result->get();
    }
}