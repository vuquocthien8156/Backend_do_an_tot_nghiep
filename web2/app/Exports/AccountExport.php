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

class AccountExport implements FromCollection, WithHeadings, ShouldAutoSize {
    use CommonTrait, Exportable ;

    public function __construct($name, $phone, $gender)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->gender = $gender;
    }

    public function collection() {
        
       $result = DB::table('users as us')->select('us.ten','us.sdt', 'us.dia_chi','us.ngay_sinh','us.gioi_tinh','us.diem_tich','us.email','us.da_xoa');
        
        if ($this->name != '' && $this->name != 'null') {
            $result->where(function($where) {
                $where->whereRaw('lower(us.ten) like ? ', ['%' . trim(mb_strtolower($this->name, 'UTF-8')) . '%']);
         
            });
        }
        if ($this->phone != '' && $this->phone != 'null') {
            $result->where(function($where) {
                $where->whereRaw('us.sdt like ? ', ['%' . $this->phone . '%']);
         
            });
        }
        if ($this->gender != '' && $this->gender != 'null') {
            $result->where('us.gioi_tinh', '=', $this->gender);
        }
        $result = $result->orderBy('us.id', 'asc')->get();

        for ($i=0; $i < count($result); $i++) {
            if ( $result[$i]->gioi_tinh == 1) {
                $result[$i]->gioi_tinh = 'Nam';
            }
            if ( $result[$i]->gioi_tinh == 2) {
                $result[$i]->gioi_tinh = 'Nữ';
            }
            if ( $result[$i]->da_xoa == 1) {
                $result[$i]->da_xoa = 'Đã xóa';
            }
            else {
                $result[$i]->da_xoa = 'Đã kích hoạt';
            }
            $result[$i]->ngay_sinh = date_format(Carbon::parse($result[$i]->ngay_sinh),'d-m-Y');
        }
        return $result;
    }

    public function headings(): array {
        return [
            'Họ tên',
            'số điện thoại',
            'ngày sinh',
            'giới tính',
            'điểm tích',
            'Địa chỉ',
            'Tên Tài khoản',
            'Trạng Thái',
        ];
    }
}

