<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use App\Models\quyen;
use App\Models\vaitro;
use App\Models\phanquyen;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductRepository {
	public function __construct(SellingVehicle $SellingVehicle) {
		$this->SellingVehicle = $SellingVehicle;
	}

	public function searchProduct() {
        $result = DB::table('sanpham as sp')->select('sp.id', 'sp.ma_sp', 'sp.ten', 'sp.gia', 'sp.so_lan_order', 'lsp.ten_loai')
        ->leftjoin('loaisp as lsp', 'lsp.id', '=', 'sp.id_loai_sp')
        ->where([
			'sp.trang_thai' => 1,
			'lsp.trang_thai' => 1,
		])->get(); 
		return $result;
    }
}