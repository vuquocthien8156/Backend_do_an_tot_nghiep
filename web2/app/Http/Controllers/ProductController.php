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
use App\Exports\ProductExport;
use Illuminate\Support\Facades\Hash;
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

    public function statisticalView() {
        $list = $this->productService->loaisp();
        return view('product.thongke',['list' => $list]);
    }

    public function searchStatistical(Request $request) {
        $name = null;
        $masp = null;
        $ma_loai = null;
        $mo_ta = null;
        $page = 1;
        $thongke = $request->get('thongke');
        if ($thongke == 'week') {
           $date = Carbon::now();
           $dayStart =  Carbon::parse($date)->startOfWeek()->toDateString();
           $dayEnd =  Carbon::parse($date)->endOfWeek()->toDateString();
           $forWeek = $this->productService->forWeek($dayStart, $dayEnd);
           $arr = [];
           $pathToResource = config('app.resource_url_path');
           for ($i=0; $i < count($forWeek); $i++) {
                if ($i < 10) {
                    $getlist = $this->productService->searchProductTK($forWeek[$i]->ma_san_pham);
                        array_push($arr, $getlist[0]);
                }
            }
            for ($i=0; $i < count($arr); $i++) {
                $arr[$i]->pathToResource = $pathToResource;
            }
        }
        if ($thongke == "month") {
            $date = Carbon::now();
            $dayStart =  Carbon::parse($date)->startOfMonth()->toDateString();
            $dayEnd =  Carbon::parse($date)->endOfMonth()->toDateString();
            $forWeek = $this->productService->forWeek($dayStart, $dayEnd);
            $arr = [];
            $pathToResource = config('app.resource_url_path');
            for ($i=0; $i < count($forWeek); $i++) {
                if ($i < 10) {
                    $getlist = $this->productService->searchProductTK($forWeek[$i]->ma_san_pham);
                        array_push($arr, $getlist[0]);
                }
            }
            for ($i=0; $i < count($arr); $i++) {
                $arr[$i]->pathToResource = $pathToResource;
            }
        }
        return response()->json(['listSearch'=>$arr]);
    }  

    public function newsView(Request $request) {
    	$id = $request->get('id');
    	$path = config('app.resource_url_path');
    	$searchNews = $this->productService->searchNews($id);
    	foreach ($searchNews as $key) {
    		$key->ten_tin_tuc = mb_strtoupper($key->ten_tin_tuc, 'UTF-8');
    	}
    	return view('product.news', ['list' => $searchNews, 'path' => $path]);
    }

    public function KM(Request $request) {
        $id = $request->get('id');
        $path = config('app.resource_url_path');
        $searchKM = $this->productService->searchKM($id);
        foreach ($searchKM as $key) {
            $key->ten_khuyen_mai = mb_strtoupper($key->ten_khuyen_mai, 'UTF-8');
        }
        return view('product.KM', ['list' => $searchKM, 'path' => $path]);
    }

	public function viewAddProduct() {
		$list = $this->productService->loaisp();
    	return view('product.addProduct',['list' => $list]);
    }

    public function detailView(Request $request) {
    	$id = $request->get('id');
    	$path = config('app.resource_url_path');
    	$searchProduct = $this->productService->searchProductById($id);
    	foreach ($searchProduct as $key) {
    		$key->ngay_ra_mat = date_format(Carbon::parse($key->ngay_ra_mat),'d-m-Y');
    	}
    	return view('productdetail.detail', ['list' => $searchProduct, 'path' => $path]);
    }

	public function searchProduct(Request $request) {
		$name = $request->get('name');
        $masp = $request->get('masp');
		$ma_loai = $request->get('ma_loai');
		$mo_ta = $request->get('mo_ta');
		$page = 1;
        if ($request->get('page') !== null) {
                $page = $request->get('page');
        }
        $pathToResource = config('app.resource_url_path');
        $infoExportExcel = ['name' => $name, 'masp' => $masp, 'ma_loai' => $ma_loai, 'mo_ta' => $mo_ta];
        $listProduct = $this->productService->searchProduct($name, $page, $ma_loai, $mo_ta, $masp);
        for ($i=0; $i < count($listProduct); $i++) { 
            $listProduct[$i]->pathToResource = $pathToResource;
            $listProduct[$i]->ngay_ra_mat = date_format(Carbon::parse($listProduct[$i]->ngay_ra_mat), 'd-m-Y');
        }
		return response()->json(['listSearch'=>$listProduct, 'infoExportExcel' => $infoExportExcel]);  
	}

	public function searchProductAPI(Request $request) {
		$name = $request->get('name');
		$ma_loai = $request->get('ma_loai');
		$mo_ta = $request->get('mo_ta');
        $page = $request->get('page');
        $ma_loai_chinh = $request->get('loai_chinh');
        $pathToResource = config('app.resource_url_path');
        $listProduct = $this->productService->searchProductAPI($name, $page, $ma_loai, $mo_ta , $ma_loai_chinh);
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
        if ($listRankProduct == '' || $listRankProduct == null) {
           return response()->json(['status' => 'ok', 'error' => 1]);
        }
        for ($i=0; $i < count($listRankProduct); $i++) {
            if ($i < 10) {
                $getlist[] = $this->productService->getlist($listRankProduct[$i]->ma_san_pham);
            }
        }
		return response()->json(['status' => 'ok', 'error' => 0, 'list'=>$getlist]);  
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
		$avatar_path = $request->get('files_edit');
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
                $subName = 'images/product/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
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
        $check1 = 0;
            $dem = count($avatar_path);
        if ($dem > 0) {
            for ($i=0; $i < $dem; $i++) {  
                $subName = 'product/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = 'images/'. $subName . '/' .$request->file('files_edit')[$i]->getClientOriginalName();
                $check = $request->file('files_edit')[$i]->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
                if ($i == 0) {
                    $result = $this->productService->addProduct($avatar_path, $ten, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);   
                }
                $getIdMax = $this->productService->getIdMax();
                $inserImage = $this->productService->inserImage($avatar_path, $getIdMax);
                if ($inserImage == false) {
                    $check1 = 1;
                    break;
                }
            }
        }else {
            $check = 1;
        }
        if ($check1 == 0) {
        	return redirect('products/manage-product');
        }else{
        	return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }

	}

    public function exportProduct(Request $request) {
        $name = $request->get('name');
        $masp = $request->get('masp');
        $ma_loai = $request->get('ma_loai');
        $mo_ta = $request->get('mo_ta');
        return Excel::download(new ProductExport($name, $masp, $mo_ta, $ma_loai), 'product-t&t.xlsx');
    }

	public function getIdSp() {
		$getIdSp = $this->productService->getIdSp();
    	for ($i=0; $i < count($getIdSp); $i++) {
    		if ($i < 10) {
    			$getlist[] = $this->productService->getlist($getIdSp[$i]->ma_san_pham);
    		}
    	}
		return response()->json(['status' => 'ok', 'error' => 0, 'list'=>$getlist]);  
	}
}
