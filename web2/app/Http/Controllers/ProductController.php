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

	public function productView(Request $request) {
        $per = $request->session()->get('id');
        $check = false;
        for($i = 0; $i < count($per); $i++) {
            if ($per[$i]->quyen_cho_phep == 4) {
                $check = true;
            }
        }
        if($check == false) {
            return "Bạn không có quyền truy cập";
        }
		$list = $this->productService->loaisp();
    	return view('product.product',['list' => $list]);
    }

    public function NewsView1(Request $request) {
        $per = $request->session()->get('id');
        $check = false;
        for($i = 0; $i < count($per); $i++) {
            if ($per[$i]->quyen_cho_phep == 6) {
                $check = true;
            }
        }
         if($check == false) {
            return "Bạn không có quyền truy cập";
        }
        $list = $this->productService->loaisp();
        return view('news.news',['list' => $list]);
    }

    public function discountView(Request $request) {
        $per = $request->session()->get('id');
        $check = false;
        for($i = 0; $i < count($per); $i++) {
            if ($per[$i]->quyen_cho_phep == 5) {
                $check = true;
            }
        }
         if($check == false) {
            return "Bạn không có quyền truy cập";
        }
        $list = $this->productService->sanPham();
        return view('discount.discount',['list' => $list]);
    }

    public function statisticalView(Request $request) {
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
           $forWeek = $this->productService->forTK($dayStart, $dayEnd);
           $arr = [];
           $pathToResource = config('app.resource_url_path');
           for ($i=0; $i < count($forWeek); $i++) {
                if ($i < 10) {
                    $getlist = $this->productService->searchProductTK($forWeek[$i]->ma_san_pham);
                    $getlist[0]->total = $forWeek[$i]->total;
                    $getlist[0]->ngay_ra_mat = date_format(Carbon::parse($getlist[0]->ngay_ra_mat),'d-m-Y');
                    array_push($arr, $getlist[0]);
                }
            }
            for ($i=0; $i < count($arr); $i++) {
                $arr[$i]->pathToResource = $pathToResource;
            }
            // dd($arr);
        }
        if ($thongke == "month") {
            $date = Carbon::now();
            $dayStart =  Carbon::parse($date)->startOfMonth()->toDateString();
            $dayEnd =  Carbon::parse($date)->endOfMonth()->toDateString();
            $forMonth = $this->productService->forTK($dayStart, $dayEnd);
            $arr = [];
            $pathToResource = config('app.resource_url_path');
            for ($i=0; $i < count($forMonth); $i++) {
                if ($i < 10) {
                    $getlist = $this->productService->searchProductTK($forMonth[$i]->ma_san_pham);
                    $getlist[0]->total = $forMonth[$i]->total;
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
        $a = url()->current();
        $b = explode("/", $a);
        $b = $b[0].'//'.$b[2].'/';
    	$id = $request->get('id');
    	$path = config('app.resource_url_path');
    	$searchNews = $this->productService->searchNews($id);
    	foreach ($searchNews as $key) {
    		$key->ten_tin_tuc = mb_strtoupper($key->ten_tin_tuc, 'UTF-8');
            $key->path = $b;
    	}
    	return view('product.news', ['list' => $searchNews, 'path' => $path]);
    }

    public function KM(Request $request) {
        $per = $request->session()->get('id');
        $check = false;
        for($i = 0; $i < count($per); $i++) {
            if ($per[$i]->quyen_cho_phep == 5) {
                $check = true;
            }
        }
        if($check == false) {
            return "Bạn không có quyền truy cập";
        }
        $id = $request->get('id');
        $path = config('app.resource_url_path');
        $searchKM = $this->productService->searchKM($id);
        foreach ($searchKM as $key) {
            $key->ten_khuyen_mai = mb_strtoupper($key->ten_khuyen_mai, 'UTF-8');
        }
        return view('product.KM', ['list' => $searchKM, 'path' => $path]);
    }

	public function viewAddProduct(Request $request) {
        $per = $request->session()->get('id');
        $check = false;
        for($i = 0; $i < count($per); $i++) {
            if ($per[$i]->quyen_cho_phep == 4) {
                $check = true;
            }
        }
        if($check == false) {
            return "Bạn không có quyền truy cập";
        }
		$list = $this->productService->loaisp();
    	return view('product.addProduct',['list' => $list]);
    }

    public function viewAddDiscount(Request $request) {
        $per = $request->session()->get('id');
        $check = false;
        for($i = 0; $i < count($per); $i++) {
            if ($per[$i]->quyen_cho_phep == 5) {
                $check = true;
            }
        }
        if($check == false) {
            return "Bạn không có quyền truy cập";
        }
        $list = $this->productService->sanPham();
        return view('discount.addDiscount',['list' => $list]);
    }

    public function viewAddNews(Request $request) {
        $per = $request->session()->get('id');
        $check = false;
        for($i = 0; $i < count($per); $i++) {
            if ($per[$i]->quyen_cho_phep == 6) {
                $check = true;
            }
        }
        if($check == false) {
            return "Bạn không có quyền truy cập";
        }
        $list = $this->productService->loaisp();
        return view('news.addNews',['list' => $list]);
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
            $listProduct[$i]->ngay_ra_mat1 = date_format(Carbon::parse($listProduct[$i]->ngay_ra_mat), 'd-m-Y');
        }
		return response()->json(['listSearch'=>$listProduct, 'infoExportExcel' => $infoExportExcel]);  
	}

    public function showMoreImg(Request $request) {
        $ma_sp = $request->get('id');
        $type = $request->get('type');
        $getImg = $this->productService->getImg($ma_sp, $type);
        for ($i=0; $i < count($getImg); $i++) { 
            $getImg[$i]->pathToResource = config('app.resource_url_path');
        }
        return \Response::json(['error' => 0, 'listImg' => $getImg]);
    }

    public function updateImgDiscount(Request $request) {
        $url = $request->file('files');
        if ($url == null || $url == '') {
            return redirect()->route('manage-discount');
        }
        $ma_sp = $request->get('id_update');
        $type =4;
        $dem = count($url);
        $now = Carbon::now();
        $check1 = 0;
        if ($dem > 0) {
            for ($i=0; $i < $dem; $i++) {  
                $subName = 'discount/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = 'images/'. $subName . '/' .$request->file('files')[$i]->getClientOriginalName();
                $check = $request->file('files')[$i]->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $url = $filename;
                $getId = $this->productService->getImg($ma_sp, $type);
                if (isset($getId[0]->url) && $i ==0) {
                    $delete = $this->productService->deleteImg($ma_sp, $type);
                }
                $inserImage = $this->productService->inserImageDiscount($url, $ma_sp);
                    if ($inserImage  == false) {
                        $check1 = 1;
                        break;
                    }
                if ($inserImage == false) {
                    $check1 = 1;
                    break;
                }
            }
            if ($check1 == 0) {
                echo "<script>alert('Thành công'); window.location='".url('discount/manage-discount')."'</script>";
            }
        }else {
            if ($check1 == 0) {
                echo "<script>alert('Thất bại'); window.location='".url('discount/manage-discount')."'</script>";
            }
        }
    }

    public function updateImgNews(Request $request) {
        $url = $request->file('files');
        if ($url == null || $url == '') {
             return redirect()->route('manage-news');
        }
        $ma_sp = $request->get('id_update');
        $type =2;
        $dem = count($url);
        $now = Carbon::now();
        $check1 = 0;
        if ($dem > 0) {
            for ($i=0; $i < $dem; $i++) {  
                $subName = 'news/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = 'images/'. $subName . '/' .$request->file('files')[$i]->getClientOriginalName();
                $check = $request->file('files')[$i]->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $url = $filename;
                $getId = $this->productService->getImg($ma_sp, $type);
                if (isset($getId[0]->url) && $i ==0) {
                    $delete = $this->productService->deleteImg($ma_sp, $type);
                }
                $inserImage = $this->productService->inserImageNews($url, $ma_sp);
                    if ($inserImage  == false) {
                        $check1 = 1;
                        break;
                    }
                if ($inserImage == false) {
                    $check1 = 1;
                    break;
                }
            }
            if ($check1 == 0) {
                echo "<script>alert('Thành công'); window.location='".url('news/manage-news')."'</script>";
            }
        }else {
            if ($check1 == 0) {
                echo "<script>alert('Thất bại'); window.location='".url('news/manage-news')."'</script>";
            }
        }
    }

    public function updateImg(Request $request) {
        $url = $request->file('files');
        if ($url == null || $url == '') {
            return redirect()->route('manage-product');
        }
        $ma_sp = $request->get('id_update');
        $type = 1;
        $dem = count($url);
        $now = Carbon::now();
        $check1 = 0;
        if ($dem > 0) {
            for ($i=0; $i < $dem; $i++) {  
                $subName = 'product/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = 'images/'. $subName . '/' .$request->file('files')[$i]->getClientOriginalName();
                $check = $request->file('files')[$i]->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $url = $filename;
                $getId = $this->productService->getImg($ma_sp,$type);
                if (isset($getId[0]->url) && $i ==0) {
                    $delete = $this->productService->deleteImg($ma_sp, $type);
                }
                $inserImage = $this->productService->inserImage($url, $ma_sp);
                    if ($inserImage  == false) {
                        $check1 = 1;
                        break;
                    }
                if ($inserImage == false) {
                    $check1 = 1;
                    break;
                }
            }
            if ($check1 == 0) {
                echo "<script>alert('Thành công'); window.location='".url('products/manage-product')."'</script>";
            }
        }else {
            if ($check1 == 0) {
                echo "<script>alert('Thất bại'); window.location='".url('products/manage-product')."'</script>";
            }
        }
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
        $getlist = [];
        for ($i=0; $i < count($listRankProduct); $i++) {
            if ($i < 10) {
                $getlist[] = $this->productService->getlist($listRankProduct[$i]->ma_san_pham)[0];
            }
        }
		return response()->json(['status' => 'ok', 'error' => 0, 'list'=>$getlist]);  
	}

	public function deleteProduct(Request $request) {
		$id = $request->get('id');
        $status = $request->get('status');
        if ($status == 1) {
            $status = 0;
        }else {
            $status = 1;
        }
		$result = $this->productService->delete($id, $status);
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
        $result = $this->productService->editProduct($avatar_path, $ten, $id, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);
        if ($result == 1) {
        	return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        }else{
        	return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
	}

    public function editDiscount(Request $request) {
        $avatar_path = $request->get('files_edit');
        $ten_khuyen_mai = $request->get('ten_khuyen_mai');
        $id = $request->get('id');
        $ma_code = $request->get('ma_code');
        $mo_ta = $request->get('mo_ta');
        $ma_sp = $request->get('ma_san_pham');
        $so_phan_tram = $request->get('so_phan_tram');
        $so_tien = $request->get('so_tien');
        $so_sp_qui_dinh = $request->get('so_sp_qui_dinh');
        $so_tien_qui_dinh_toi_thieu = $request->get('so_tien_qui_dinh_toi_thieu');
        $gioi_han_so_code = $request->get('gioi_han_so_code');
        $ngay_bat_dau = $request->get('ngay_bat_dau');
        $ngay_ket_thuc = $request->get('ngay_ket_thuc');
        $id_now = session()->get('user_id');
        $type = $request->get('type');
        $so_sp_tang_kem = $request->get('so_sp_tang_kem');
        $now = Carbon::now();
        if ($request->file('files_edit') != null || $request->file('files_edit') != '') {
                $subName = 'discount/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = 'images/' . $subName . '/' . $request->file('files_edit')->getClientOriginalName();
                $check = $request->file('files_edit')->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
        }
        $result = $this->productService->editDiscount($ten_khuyen_mai, $id,$ma_code,$mo_ta,$so_phan_tram ,$so_tien ,$so_sp_qui_dinh ,$so_tien_qui_dinh_toi_thieu,$gioi_han_so_code ,$ngay_bat_dau ,$ngay_ket_thuc ,$id_now,$type, $so_sp_tang_kem, $avatar_path);
        if ($result == 1) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        }else{
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function editNews(Request $request) {
        $avatar_path = $request->get('files_edit');
        $ten = $request->get('ten');
        $id = $request->get('id');
        $ND = $request->get('ND');
        $date = $request->get('date');
        $now = Carbon::now();
        if ($request->file('files_edit') != null || $request->file('files_edit') != '') {
                $subName = 'news/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = 'images/'. $subName . '/' . $request->file('files_edit')->getClientOriginalName();
                $check = $request->file('files_edit')->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
        }
        $result = $this->productService->editNews($avatar_path, $ten, $id, $ND, $date);
        if ($result == 1) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        }else{
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

	public function addProduct(Request $request) {
		$avatar_path = $request->file('files');
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
        if ($avatar_path == null) {
            $avatar_path = [];
        }
        $dem = count($avatar_path);
        if ($dem > 0) {
            for ($i=0; $i < $dem; $i++) {  
                $subName = 'product/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = 'images/'. $subName . '/' .$request->file('files')[$i]->getClientOriginalName();
                $check = $request->file('files')[$i]->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
                if ($i == 0) {
                    $result = $this->productService->addProduct($avatar_path, $ten, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);
                    $check1 = 0;   
                }
                if ($i > 0) {
                    $getIdMax = $this->productService->getIdMax();
                    $inserImage = $this->productService->inserImage($avatar_path, $getIdMax);
                    if ($inserImage == false) {
                        $check1 = 1;
                        break;
                    }
                }
            }
        }else {
            $check = 1;
            echo "<script>alert('Thất bại'); window.location='".url('products/manage-product')."'</script>";
        }
        if ($check1 == 0) {
        	echo "<script>alert('Thành công'); window.location='".url('products/manage-product')."'</script>";
        }else{
        	return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function addDiscount(Request $request) {
        $avatar_path = $request->file('files');
        $now = Carbon::now();
        $type = $request->get('type');
        $ma = $request->get('ma');
        $ten = $request->get('ten');
        $MT = $request->get('MT');
        $SPT = $request->get('SPT');
        $ST = $request->get('ST');
        $SSPQD = $request->get('SSPQD');
        $STQDTT = $request->get('STQDTT');
        $NBD = $request->get('NBD');
        $NKT = $request->get('NKT');
        $GHSC = $request->get('GHSC');
        $SSPTK = $request->get('SSPTK');
        $SP = $request->get('SP');
        if ($avatar_path == null) {
            $avatar_path = [];
        }
        $check1 = 0;
            $dem = count($avatar_path);
        if ($dem > 0) {
            for ($i=0; $i < $dem; $i++) {  
                $subName = 'discount/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = 'images/'. $subName . '/' .$request->file('files')[$i]->getClientOriginalName();
                $check = $request->file('files')[$i]->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
                if ($i == 0) {
                    $result = $this->productService->addDiscount($avatar_path,$now,$type,$ma, $ten,$MT, $SPT, $ST, $SSPQD, $STQDTT, $NBD ,$NKT, $GHSC,$SSPTK,$SP );
                    $check1 = 0;   
                }
                if ($i > 0) {
                     $getIdMax = $this->productService->getIdMaxDiscount();
                    $inserImage = $this->productService->inserImageDiscount($avatar_path, $getIdMax);
                    if ($inserImage == false) {
                        $check1 = 1;
                        break;
                    }
                }
            }
        }else {
            $check = 1;
            echo "<script>alert('Thất bại vui lòng thử lại'); window.location='".url('discount/manage-discount')."'</script>";
        }
        if ($check1 == 0) {
           echo "<script>alert('Thành công'); window.location='".url('discount/manage-discount')."'</script>";
        }else{
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function addNews(Request $request) {
        $avatar_path = $request->file('files');
        $now = Carbon::now();
        $ten = $request->get('ten');
        $ND = $request->get('ND');
        $NĐ = $request->get('NĐ');
        $check1 = 0;
        if ($avatar_path == null) {
            $avatar_path = [];
        }
        $ngay_tao = Carbon::now();
        $dem = count($avatar_path);
        if ($dem > 0) {
            for ($i=0; $i < $dem; $i++) {  
                $subName = 'news/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = 'images/'. $subName . '/' .$request->file('files')[$i]->getClientOriginalName();
                $check = $request->file('files')[$i]->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
                if ($i == 0) {
                    $result = $this->productService->addNews($ten, $ND, $ngay_tao, $avatar_path, $NĐ);
                    $check1 = 0;   
                }
                if ($i > 0) {
                    $getIdMax = $this->productService->getIdMaxNews();
                    $inserImage = $this->productService->inserImage($avatar_path, $getIdMax);
                    if ($inserImage == false) {
                        $check1 = 1;
                        break;
                    }
                }
            }
        }else {
            $check = 1;
            echo "<script>alert('Thất bại'); window.location='".url('news/manage-news')."'</script>";
        }
        if ($check1 == 0) {
           echo "<script>alert('Thành công'); window.location='".url('news/manage-news')."'</script>";
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
    			$getlist[] = $this->productService->getlist($getIdSp[$i]->ma_san_pham)[0];
    		}
    	}
		return response()->json(['status' => 'ok', 'error' => 0, 'list'=>$getlist]);  
	}

    public function searchDiscount(Request $request) {
        $type = $request->get('type');
        $listDiscount = $this->productService->searchDiscount($type);
        $pathToResource = config('app.resource_url_path');
        for ($i=0; $i < count($listDiscount); $i++) { 
            $listDiscount[$i]->pathToResource = $pathToResource;
            $listDiscount[$i]->ngay_BD = isset($listDiscount[$i]->ngay_bat_dau) ? date_format(Carbon::parse($listDiscount[$i]->ngay_bat_dau), 'd-m-Y') : null;
            $listDiscount[$i]->ngay_KT = isset($listDiscount[$i]->ngay_ket_thuc) ? date_format(Carbon::parse($listDiscount[$i]->ngay_ket_thuc), 'd-m-Y') :null;
             $listDiscount[$i]->pathToResource = $pathToResource;
        }
       return response()->json(['listSearch'=>$listDiscount]);
    }

    public function searchNews(Request $request) {
        $name = $request->get('name');
        $searchNews = $this->productService->searchNews1($name);
        $pathToResource = config('app.resource_url_path');
        for ($i=0; $i < count($searchNews); $i++) { 
            $searchNews[$i]->pathToResource = $pathToResource;
            $searchNews[$i]->ngay_dang = date_format(Carbon::parse($searchNews[$i]->ngay_dang), 'd-m-Y');
        }
       return response()->json(['listSearch'=>$searchNews]);
    }

    public function deleteDiscount(Request $request) {
        $id = $request->get('id');
        $status = $request->get('status');
        if ($status == 1) {
            $status = 0;
        }else {
            $status = 1;
        }
        $result = $this->productService->deleteDiscount($id, $status);
        if ($result != 0) {
             return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        }
        return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
    }

    public function deleteNews(Request $request) {
        $id = $request->get('id');
        $status = $request->get('status');
        if ($status == 1) {
            $status = 0;
        }else {
            $status = 1;
        }
        $result = $this->productService->deleteNews($id, $status);
        if ($result != 0) {
             return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        }
        return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
    }
}
