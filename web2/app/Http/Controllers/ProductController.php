<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Helpers\ConfigHelper;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Traits\CommonTrait;
use Excel;

class ProductController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(ProductService $productService) {
		$this->productService = $productService;
	}

	public function productView() {
		$list = $this->productService->loaisp();
    	return view('product.product',['list' => $list]);
    }

	public function viewAddProduct() {
		$list = $this->productService->loaisp();
    	return view('product.addProduct',['list' => $list]);
    }

	public function searchProduct(Request $request) {
		$a = $request->get('gio_hang');
		$b = $a['topping'][0];

		dd($a);
		$name = $request->get('name');
		$ma_loai = $request->get('ma_loai');
		$mo_ta = $request->get('mo_ta');
		$page = 1;
        if ($request->get('page') !== null) {
                $page = $request->get('page');
        }
        $pathToResource = config('app.resource_url_path');
        $listProduct = $this->productService->searchProduct($name, $page, $ma_loai, $mo_ta);
        for ($i=0; $i < count($listProduct); $i++) { 
             $listProduct[$i]->pathToResource = $pathToResource;
        }
		return response()->json(['listSearch'=>$listProduct]);  
	}

	public function searchProductAPI(Request $request) {
		$name = $request->get('name');
		$ma_loai = $request->get('ma_loai');
		$mo_ta = $request->get('mo_ta');
        $page = $request->get('page');
        $pathToResource = config('app.resource_url_path');
        $listProduct = $this->productService->searchProductAPI($name, $page, $ma_loai, $mo_ta);
        for ($i=0; $i < count($listProduct); $i++) { 
        	$list[] = $listProduct[$i];
        }
        for ($i=0; $i < count($list); $i++) { 
             $list[$i]->pathToResource = $pathToResource;
        }
		return response()->json(['status' => 'ok', 'error' => 0, 'list'=>$list]);  
	}

	public function searchRankProduct(Request $request) {
		$page = 1;
        if ($request->get('page') !== null) {
                $page = $request->get('page');
        }
        $pathToResource = config('app.resource_url_path');
        $listRankProduct = $this->productService->searchRankProduct();
        for ($i=0; $i < count($listRankProduct); $i++) { 
             $listRankProduct[$i]->pathToResource = $pathToResource;
        }
		return response()->json(['status' => 'ok', 'error' => 0, 'list'=>$listRankProduct]);  
	}

	public function deleteProduct(Request $request) {
		$id = $request->get('id');
		$result = $this->productService->delete($id);
		if ($result != 0) {
			 return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
		}
		return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
	}

	public function twoDigitNumber($number) {
		return $number < 10 ? '0'.$number : $number;
    }

	public function editProduct(Request $request) {
		$avatar_path = $request->get('avatar');
		$ten = $request->get('ten');
		$id = $request->get('id');
		$so_lan_order = $request->get('so_lan_order');
		$ma = $request->get('ma');
		$gia_goc = $request->get('gia_goc');
		$gia_size_vua = $request->get('gia_size_vua');
		$gia_size_lon = $request->get('gia_size_lon');
		$loaisp = $request->get('loaisp');
		$ngay_ra_mat = $request->get('ngay_ra_mat');
		$mo_ta = $request->get('mo_ta');
		$now = Carbon::now();
		if ($request->file('files_edit') != null || $request->file('files_edit') != '') {
                $subName = 'user/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename =  $subName . '/' . $request->file('files_edit')->getClientOriginalName();
                $check = $request->file('files_edit')->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
        }
        $result = $this->productService->editProduct($avatar_path, $ten, $id, $so_lan_order, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);
        if ($result == 1) {
        	return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        }else{
        	return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
        // dd($avatar_path, $ten, $id, $so_lan_order, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);
	}

	public function addProduct(Request $request) {
		$avatar_path = $request->file('files_edit');
		$now = Carbon::now();
		$ten = $request->get('ten');
		$ma = $request->get('ma');
		$gia_goc = $request->get('gia_goc');
		$gia_size_vua = $request->get('gia_size_vua');
		$gia_size_lon = $request->get('gia_size_lon');
		$loaisp = $request->get('loaisp');
		$ngay_ra_mat = $request->get('ngay_ra_mat');
		$mo_ta = $request->get('mo_ta');
		if ($request->file('files_edit') != null || $request->file('files_edit') != '') {
                $subName = 'product/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename =  $subName . '/' . $request->file('files_edit')->getClientOriginalName();
                $check = $request->file('files_edit')->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
        }
        $result = $this->productService->addProduct($avatar_path, $ten, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);
        if ($result == true) {
        	return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        }else{
        	return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }

	}

	public function getIdSp() {
		$getIdSp = $this->productService->getIdSp();
		$list=[];
		for ($i=0; $i < count($getIdSp); $i++) { 
			$amount[] = $this->productService->getAmount($getIdSp[$i]->ma_san_pham); // lấy số lượng theo id
		}
		//sắp xếp theo kết quả đã count
		for ($i = 0; $i < count($amount) - 1; $i++)
    	{
	        $max = $i;
	        for ($j = $i + 1; $j < count($amount); $j++){
	            if ($amount[$j] > $amount[$max]){
	                $max = $j;
	            }
	        }
	        $temp = $amount[$i];
	        $amount[$i] = $amount[$max];
	        $amount[$max] = $temp;
    	}

    	//lấy id kết quả đã count
    	for ($i=0; $i < count($amount); $i++) {
    		$b[] = $amount[$i][0]->ma_san_pham;
    	}

    	for ($i = 0; $i < count($b) - 1; $i++)
    	{
	        $min = $i;
	        for ($j = $i + 1; $j < count($b); $j++){
	            if ($b[$j] < $b[$min]){
	                $min = $j;
	            }
	        }
	        $temp = $b[$i];
	        $b[$i] = $b[$min];
	        $b[$min] = $temp;
    	}

    	for ($i=0; $i < count($b); $i++) {
    		if ($i < 10) {
    			$getlist[] = $this->productService->getlist($b[$i]);
    		}
    	}
		return response()->json(['status' => 'ok', 'error' => 0, 'list'=>$getlist]);  
	}
}
