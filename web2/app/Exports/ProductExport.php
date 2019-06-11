<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Services\VehicleService;
use App\Repositories\VehicleRepository;
use Illuminate\Support\Facades\DB;
use App\Enums\EStatus;
use App\Enums\EUser;
use Illuminate\Support\Carbon;
use App\Enums\EDateFormat;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Enums\EVehicleStatus;
use App\Enums\EVehicleAccredited;
use App\Enums\EVehicleType;

class ProductExport implements FromCollection, WithHeadings, ShouldAutoSize {
    use CommonTrait, Exportable ;

    public function __construct($name, $masp, $mo_ta, $ma_loai)
    {
        $this->name = $name;
        $this->masp = $masp;
        $this->mo_ta = $mo_ta;
        $this->ma_loai = $ma_loai;
    }

    public function collection() {
        
        $result = DB::table('SanPham as sp')->select('sp.ten', 'sp.ma_chu','lsp.ten_loai_sp', 'sp.gia_san_pham', 'sp.gia_vua', 'sp.gia_lon', 'sp.so_lan_dat' , 'sp.mo_ta', 'sp.ngay_ra_mat', 'sp.daxoa', 'sp.hinh_san_pham' )
        ->leftjoin('LoaiSanPham as lsp', 'lsp.ma_loai_sp', '=', 'sp.loai_sp')
        ->where([
            'lsp.da_xoa' => 0,
        ]);

        if ($this->name != '' && $this->name != 'null') {
            $result->where(function($where) {
                $where->whereRaw('lower(sp.ten) like ? ', ['%' . trim(mb_strtolower($this->name, 'UTF-8')) . '%']);
         
            });
        }

        if ($this->ma_loai != '' && $this->ma_loai != 'null') {
             $result->where('sp.loai_sp', '=', $this->ma_loai);
        }

        if ($this->mo_ta != '' && $this->mo_ta != 'null') {
            $result->where(function($where) {
                $where->whereRaw('lower(sp.mo_ta) like ? ', ['%' . trim(mb_strtolower($this->mo_ta, 'UTF-8')) . '%']);
         
            });
        }

        if ($this->masp != '' && $this->masp != 'null') {
            $result->where(function($where) {
                $where->whereRaw('lower(sp.ma_chu) like ? ', ['%' . trim(mb_strtolower($this->masp, 'UTF-8')) . '%']);
         
            });
        }
        $result = $result->orderBy('sp.ma_so', 'asc')->get();
        for ($i=0; $i < count($result); $i++) { 
            if ($result[$i]->daxoa == 1) {
                $result[$i]->daxoa = "Đã xóa";
            }else {
                $result[$i]->daxoa = "Đã kích hoạt";
            }
            $result[$i]->ngay_ra_mat = date_format(Carbon::parse($result[$i]->ngay_ra_mat), 'd-m-Y');
        }
        return $result;
    }

    public function headings(): array {
        return [
            'Tên sản phẩm',
            'Mã sản phẩm',
            'Loại sản phẩm',
            'Giá gốc',
            'Giá size vừa',
            'Giá size lớn',
            'Số lượng order',
            'Mô tả',
            'Ngày ra mắt',
            'Trạng thái',
        ];
    }
}

