<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Enums\EUserRole;
use App\Helpers\ConfigHelper;
use App\Services\LoginService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Services\PermissionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Excel;

class LoginController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(LoginService $loginService, PermissionService $permissionService) {
		$this->loginService = $loginService;
		$this->permissionService = $permissionService;
	}

	public function loginView(Request $request) {
	  	return view('login.login2');
	}
	
	public function check(Request $request) {
		$user = $request->get("username");
		// $id_fb = $request->get("id_fb");
		$check = $this->loginService->check($user);
		if (isset($check[0]->user_id)) {
			return response()->json(['status' => 'ok', 'error' => 0,'email' => $check[0]->email, 'id_fb' => $check[0]->fb_id, 'phone' => $check[0]->sdt]);
		}
		else
		{
			return response()->json(['status' => 'error', 'error' => 1]);
		}
	}

	public function login(Request $request) {  
		$user = $request->get("username");
		$pass = md5($request->get("password"));
		$check = $this->loginService->login($user, $pass);
		if (isset($check[0]->user_id)) {
			session()->put('id',$check[0]->user_id);
			session()->put('name',$check[0]->ten);
			session()->put('login',true);
			$getRoll = $this->permissionService->getRoll($check[0]->user_id);
			session()->put('ten_vai_tro',$getRoll);
			return response()->json(['status' => 'ok', 'error' => 0, $check]);
		}
		else
		{
			return response()->json(['status' => 'error','error' => 1]);
		}
	}

	public function loginAPI(Request $request) {  
		$user = $request->get("username");
		$pass = $request->get("password");
		$check = $this->loginService->login($user, $pass);
		if (isset($check[0]->user_id)) {
			return response()->json(['status' => 'ok', 'error' => 0, 'info' => $check[0]]);
		}
		else
		{
			return response()->json(['status' => 'error','error' => 1]);
		}
	}

	public function getInfoByEmail(Request $request){
		$email = $request->get("email");
		$check = $this->loginService->getInfoByEmail($email);
		if (isset($check[0]->user_id)) {
			return response()->json(['status' => 'ok', 'error' => 0, 'info' => $check[0]]);
		}
		else
		{
			return response()->json(['status' => 'error','error' => 1]);
		}
	}

	public function loginsdt(Request $request) {
	if ($request->session()->has('name') == true) {
	  		return redirect()->route('home');
	  	}  
		$user = $request->get("username");
		$check = $this->loginService->loginsdt($user);
		if (isset($check[0]->user_id)) {
			session()->put('id',$check[0]->user_id);
			session()->put('name',$check[0]->ten);
			session()->put('login',true);
			session()->put('vaitro',$check[0]->id_vai_tro);
			session()->put('quyen_he_thong',$check[0]->quyen_he_thong);
			return response()->json(['status' => 'ok', 'error' => 0, 'info' => $check]);
		}
		else{
			return response()->json(['status' => 'error','error' => 1]);
		}
	}

	public function loginsdtAPI(Request $request) {
		$user = $request->get("username");
		$check = $this->loginService->loginsdt($user);
		if (isset($check[0]->user_id)) {
			return response()->json(['status' => 'ok', 'error' => 0, 'info' => $check[0]]);
		}
		else{
			return response()->json(['status' => 'error','error' => 1]);
		}
	}

	public function logout(Request $request) {  
		session()->flush();
		return redirect('loginView');
	}

	public function twoDigitNumber($number) {
		return $number < 10 ? '0'.$number : $number;
    }
	
	public function uploadImage(Request $request){
		$now = Carbon::now();
		$second = $now->second;
		$minute = $now->minute;
		$hour = $now->hour;
		$date = $now->day;
		$month = $now->month;
		$year = $now->year;
		$S = $second*$minute*$hour*$date*$month*$year;
		if ($request->file('avatar') != null || $request->file('avatar') != ''){
	            $subName = 'account/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
	            $destinationPath = config('app.resource_physical_path');
	            $pathToResource = config('app.resource_url_path');
	            $nameImg = 'Account_Img'.$S.strstr($request->file('avatar')->getClientOriginalName(), '.');
	            $check = $request->file('avatar')->move($destinationPath.'/'.$subName, $nameImg);
            	if (!file_exists($check)) {
                	return response()->json(['filename' => 'null']);
            	}

            return response()->json(['filename' => 'images/'.$subName.'/' . $nameImg]);
		}
	}

	public function uploadManyImage(Request $request){
		$now = Carbon::now();
		$second = $now->second;
		$minute = $now->minute;
		$hour = $now->hour;
		$date = $now->day;
		$month = $now->month;
		$year = $now->year;
		$a = $request->file('imgEv');
		$arr = [];
		if ($a != null || $a != ''){
			for($i = 0; $i < count($a); $i++) {
				$S = $second*$minute*$hour*$date*$month*$year;
				$subName = 'EV/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
	            $destinationPath = config('app.resource_physical_path');
	            $pathToResource = config('app.resource_url_path');
	            $nameImg = 'EV_Img'.$S.$i.strstr($a[$i]->getClientOriginalName(), '.');
	            $check = $a[$i]->move($destinationPath.'/'.$subName, $nameImg);
	            array_push($arr, 'images/'.$subName.'/' . $nameImg);
            	if (!file_exists($check)) {
                	return response()->json(['status' => 'error', 'error' => 0]);
            	}
			}
            return response()->json(['status' => 'Success', 'error' => 0, 'arr' => $arr]);
		}
	}

	public function requestUpdateInfo(Request $request) {
		$id = $request->get('id');
		$email = $request->get('email');
		$name = $request->get('name');
		$phone = $request->get('phone');
		$gender = $request->get('gender');
		$dob = $request->get('birth_day');
		$avatar = $request->get('avatar_path');
      
        $update =  $this->loginService->updateInfo($email, $name, $phone, $gender, $dob, $avatar, $id);
        if ($update > 0) {
        	return response()->json(['status' => 'ok', 'error' => 0]);
        }else {
        	return response()->json(['status' => 'upload fall', 'error' => 1]);
        }
	}
	public function getLikedProduct(Request $request) {
		$id = $request->get('id');
		$getLikedProduct =  $this->loginService->getLikedProduct($id);
		return response()->json(['status' => 'ok', 'error' => 0, 'list' => $getLikedProduct]);
	}

	public function requestLike(Request $request) {
		$id_product = $request->get('id_product');
		$id_user = $request->get('id_user');
		$like = $request->get('like');
		$temp = 0;
		$getLike =  $this->loginService->getLike();
		for ($i=0; $i < count($getLike); $i++) { 
			if ($getLike[$i]->ma_san_pham == $id_product && $getLike[$i]->ma_khach_hang == $id_user) {
				$temp = 1;
				break;
			}
			else {
				$temp = 0;
			}
		}
		if ($temp == 1) {
			$updateLike =  $this->loginService->updateLike($id_product, $id_user, $like);
			if ($updateLike != 0) {
				return response()->json(['status' => 'ok', 'error' => 0, 'message' => 'update like']);
			}else{
				return response()->json(['status' => 'fail', 'error' => 1]);
			}
		}else {
			$insertLike =  $this->loginService->insertLike($id_product, $id_user, $like);
			if ($insertLike == true) {
				return response()->json(['status' => 'ok', 'error' => 0, 'message' => 'insert like']);
			}else{
				return response()->json(['status' => 'fail', 'error' => 1]);
			}
		}
	}

	public function getAllOrder(Request $request) {
		$id_KH = $request->get('id_KH');
		$getAllOrder =  $this->loginService->getAllOrder($id_KH);
		$getUser = $this->loginService->getUser2($id_KH);
		for ($i=0; $i < count($getAllOrder); $i++) { 
			$getStatusOrder = $this->loginService->getStatusOrder($getAllOrder[$i]->ma_don_hang);
			$getAllOrder[$i]->trang_thai = $getStatusOrder;			
		}

		return response()->json(['status' => 'ok', 'error' => 0, 'Order' => $getAllOrder]);
	}

	public function addOrder(Request $request) {
		$thong_tin_DH = $request->get('thong_tin_DH');
		$ma_kh = $thong_tin_DH['ma_khach_hang'];
		$thong_tin_ship = $thong_tin_DH['thong_tin_giao_hang'];
		$khuyen_mai = $thong_tin_DH['khuyen_mai'];
		$phi_ship = $thong_tin_DH['phi_ship'];
		$tong_tien = $thong_tin_DH['tong_tien'];
		$point = (int)$tong_tien / 10000;
		$ghi_chu = $thong_tin_DH['ghi_chu'];
		$so_diem = $thong_tin_DH['so_diem'];
		$phuong_thuc = $thong_tin_DH['phuong_thuc_thanh_toan'];
		$ngay_lap = Carbon::now();
		$getMaxIdOrder =  $this->loginService->getMaxIdOrder();
		$ma_chu = 'DHQTPT'. ($getMaxIdOrder + 1);
		$Detail = $request->get('Detail');
		$check = true;
		
		if ($phuong_thuc == 2) {
			$insertOrder = $this->loginService->insertOrder($thong_tin_ship, $ma_kh, $khuyen_mai, $phi_ship, $tong_tien, $ghi_chu, $ngay_lap, $ma_chu);
			$getMaxIdOrder =  $this->loginService->getMaxIdOrder();
			$getPoint = $this->loginService->getPoint($ma_kh);
			$totalPoint = (int)$getPoint[0]->diem_tich - (int)$so_diem;
			
			$insertStatusOrder = $this->loginService->insertStatusOrder($getMaxIdOrder);

			for ($i=0; $i < count($Detail); $i++) {
				$topping = $Detail[$i]['topping'];
				$insertOrderDetail = $this->loginService->insertOrderDetail($getMaxIdOrder, $Detail[$i]['ma_san_pham'], $Detail[$i]['so_luong'], $Detail[$i]['don_gia'], $Detail[$i]['gia_khuyen_mai'], $Detail[$i]['thanh_tien'], $Detail[$i]['ghi_chu'], $Detail[$i]['kich_co']);
				$getMaxIdOrderDetail =  $this->loginService->getMaxIdOrderDetail();
				for ($j=0; $j < count($topping); $j++) { 
					$insertToppingOrder = $this->loginService->insertToppingOrder(
						$getMaxIdOrderDetail,
						$Detail[$i]['ma_san_pham'], $topping[$j]['so_luong'], $topping[$j]['don_gia']);
					if ($insertToppingOrder == false) {
						return response()->json(['status' => 'fail', 'error' => 1]);
					}
				}
			}
			$updatePoint = $this->loginService->addPoint($ma_kh, $totalPoint);
			$addLog = $this->loginService->addLog($ma_kh, $getMaxIdOrder, $phuong_thuc, $ngay_lap, $totalPoint);
			$deleteCart = $this->loginService->deleteCartCustomer($ma_kh);
			if ($deleteCart > 0) {
		 		return response()->json(['status' => 'Success', 'error' => 0]);
			}else {
				return response()->json(['status' => 'fail', 'error' => 1]);
			}

		}
		if ($phuong_thuc == 1) {
			$insertOrder = $this->loginService->insertOrder($thong_tin_ship, $ma_kh, $khuyen_mai, $phi_ship, $tong_tien, $ghi_chu, $ngay_lap, $ma_chu);
			$getMaxIdOrder =  $this->loginService->getMaxIdOrder();
			$insertStatusOrder = $this->loginService->insertStatusOrder($getMaxIdOrder);
			for ($i=0; $i < count($Detail); $i++) {
				$topping = $Detail[$i]['topping'];
				$insertOrderDetail = $this->loginService->insertOrderDetail($getMaxIdOrder, $Detail[$i]['ma_san_pham'], $Detail[$i]['so_luong'], $Detail[$i]['don_gia'], $Detail[$i]['gia_khuyen_mai'], $Detail[$i]['thanh_tien'], $Detail[$i]['ghi_chu'], $Detail[$i]['kich_co']);
				$getMaxIdOrderDetail =  $this->loginService->getMaxIdOrderDetail();
				if ($insertOrderDetail == false) {
					return response()->json(['status' => 'fail', 'error' => 1]);
				}
				for ($j=0; $j < count($topping); $j++) { 
					$insertToppingOrder = $this->loginService->insertToppingOrder($getMaxIdOrderDetail, $topping[$j]['ma_san_pham'], $topping[$j]['so_luong'], $topping[$j]['don_gia']);
					if ($insertToppingOrder == false) {
						return response()->json(['status' => 'fail', 'error' => 1]);
					}
				}
			}
			$getStatusOrder = $this->loginService->getStatusOrder($getMaxIdOrder);
			$deleteCart = $this->loginService->deleteCartCustomer($ma_kh);
			return response()->json(['status' => 'Success', 'Message' => 'Please wait update status']);
		}
	}

	public function updateIdFB(Request $request)
    {
    	$id_fb = $request->get('id_fb');
    	$email = $request->get('email');
    	$type = $request->get('type');
        $updateIdFB =  $this->loginService->updateIdFB($id_fb, $email);
        $getInfo = $this->loginService->getInfo($id_fb);
        if (isset($getInfo[0]->ten)) {
        	return response()->json(['status' => 'ok', 'error' => 0, 'infoUser' => $getInfo]);
        }
        return response()->json(['status' => 'fail', 'error' => 1]);
    }

    public function updateEmail(Request $request)
    {
    	$id_fb = $request->get('id_fb');
    	$email = $request->get('email');
    	$updateIdFB =  $this->loginService->updateIdFB($id_fb, $email);
    	$insertPass = $this->loginService->insertPass($id_fb);
        if (isset($getInfo[0]->ten)) {
        	return response()->json(['status' => 'ok', 'error' => 0, 'infoUser' => $getInfo]);
        }
        return response()->json(['status' => 'fail', 'error' => 1]);
    }

    public function news (Request $request) {
    	$page = $request->get('page');
    	$getNews = $this->loginService->news($page);
    	$pathToResource = config('app.resource_url_path');
    	for ($i=0; $i < count($getNews); $i++) { 
        	$list[] = $getNews[$i];
        }
        for ($i=0; $i < count($list); $i++) { 
             $list[$i]->pathToResource = $pathToResource;
        }
		return response()->json(['status' => 'Success', 'error' => 0, 'listNews'=>$list]);
    }

    public function productType (Request $request) {
    	$page = $request->get('page');
    	$getProductType = $this->loginService->productType($page);
    	$pathToResource = config('app.resource_url_path');
    	 for ($i=0; $i < count($getProductType); $i++) { 
        	$list[] = $getProductType[$i];
        }
        for ($i=0; $i < count($list); $i++) { 
             $list[$i]->pathToResource = $pathToResource;
        }
		return response()->json(['status' => 'ok', 'error' => 0, 'listCatalogy'=>$list]);
    }

    public function addCart(Request $request) {
    	$idCustomer = $request->get('idCustomer');
    	$objectCart = $request->get('cart');
    	$ma_sp = $objectCart['ma_san_pham'];
    	$so_luong = $objectCart['so_luong'];
    	$size = $objectCart['kich_co'];
    	$note = $objectCart['ghi_chu'];

    	$topping = $objectCart['topping'];
    	$getCart = $this->loginService->getCart();
    	$check = 0;
    	$loop = 0;
	    for ($i=0; $i < count($getCart); $i++) {
	    	if ($idCustomer == $getCart[$i]->ma_khach_hang && $ma_sp == $getCart[$i]->ma_san_pham && $size == $getCart[$i]->kich_co) {
	    		$getDetailCart = $this->loginService->getDetailCart($getCart[$i]->ma_gio_hang);
	    		
	    			$a=0;
			    	for ($y=0; $y < count($getDetailCart); $y++) {
			    		for ($k=0; $k < count($topping); $k++) {
			    			if (count($topping) == count($getDetailCart)) {
			    				
			    			 	if ($topping[$k]['ma_san_pham'] == $getDetailCart[$y]->ma_san_pham && $topping[$k]['so_luong'] == $getDetailCart[$y]->so_luong) {
			    		 			$a = $a+1;
			    				}
			    			}
			    		}
			    	}
			    	if ($a == count($getDetailCart) && $a == count($topping)) {
			    		$getQuantity = $this->loginService->getSL($getCart[$i]->ma_gio_hang);
			    		$sl = $so_luong + $getQuantity[0]->so_luong;
			    		$updateQuantityCart = $this->loginService->updateQuantityCart($getCart[$i]->ma_gio_hang, $sl);
			    		if ($updateQuantityCart == 1) {
			    			return response()->json(['status' => 'Success', 'error' => 0]);
			    		}
			    		return response()->json(['status' => 'fail', 'error' => 1]);
			    	}else {
			    		$loop = $loop + 1; 
			    	}
	    	}else {
	    		$loop = $loop + 1; 
	    	}
	    }
	    
	    if ($loop == count($getCart)) {
	    	$insertCart = $this->loginService->insertCart($idCustomer, $ma_sp, $so_luong, $size, $note);
	    	if ($topping != '' && $topping != null && $topping != []) {
	    		$selectMaxId = $this->loginService->selectMaxId();
				$insertTopping = $this->loginService->insertTopping($selectMaxId, $topping);
	    	}
			if ($insertCart == true) {
					return response()->json(['status' => 'Success', 'error' => 0]);
				}
			return response()->json(['status' => 'fail', 'error' => 1]);
	    }
    }

    public function updateCart (Request $request) {
    	$idCart = $request->get('ma_gio_hang');
    	$so_luong = $request->get('so_luong');
    	$size = $request->get('kich_co');
    	$note = $request->get('ghi_chu');

    	$topping = $request->get('topping');
    	$updateCartOfCustomer = $this->loginService->updateCartOfCustomer($idCart, $so_luong, $size, $note);
    	
    	if ($updateCartOfCustomer == 1) {
			    $deleteToppingOfCart = $this->loginService->deleteToppingOfCart($idCart);
			    if($deleteToppingOfCart >= 0){
		    		if($topping != null){
				    		$insertTopping = $this->loginService->insertTopping($idCart, $topping);
				    		if($insertTopping > 0)
				    			return response()->json(['status' => 'Success', 'error' => 0]);
				    		else
				    			return response()->json(['status' => 'failAdd', 'error' => 1]);
				    }
				    else
				    	return response()->json(['status' => 'Success', 'error' => 0]);
		    	}
			    else
	    			return response()->json(['status' => 'failDelete', 'error' => 1]);
	    }
    	return response()->json(['status' => 'failCart', 'error' => 1]);
    }

    public function deleteCart(Request $request) {
    	$id_GH = $request->get('id_GH');
    	$deleteCart = $this->loginService->deleteCart($id_GH);
    	$deleteTopping = $this->loginService->deleteToppingOfCart($id_GH);
    	if ($deleteCart > 0 && $deleteTopping >= 0) {
    		return response()->json(['status' => 'Success', 'error' => 0 ]);
    	}
    	return response()->json(['status' => 'fail', 'error' => 1]);
    }

    public function deleteCartCustomer(Request $request) {
    	$id_KH = $request->get('id_KH');
    	$deleteCartCustomer = $this->loginService->deleteCartCustomer($id_KH);
    	if ($deleteCartCustomer == 1) {
    		return response()->json(['status' => 'Success', 'error' => 0]);
    	}
    	return response()->json(['status' => 'fail', 'error' => 1]);
    }

    public function updateToppingCart(Request $request) {
    	$objUpdate = $request->get('obj');
    	$ma_gio_hang = $objUpdate['ma_gio_hang'];
    	$topping = $objUpdate['topping'];
    	for ($i=0; $i < count($topping); $i++) { 
    		$ma_topping = $topping[$i][0];
    		$ten_topping = $topping[$i][1];
    		$gia_san_pham = $topping[$i][2];
    		$so_luong_topping = $topping[$i][3];
    		$updateCart = $this->loginService->updateCart($ma_gio_hang, $ma_topping, $ten_topping, $so_luong_topping);
	    	if ($updateCart == true) {
	    		return response()->json(['status' => 'Success', 'error' => 0]);
	    	}
    		return response()->json(['status' => 'fail', 'error' => 1]);
    	}
    }

    public function getCartOfCustomer(Request $request) {
    	$id_KH = $request->get('id_KH');
    	$getCartOfCustomer = $this->loginService->getCartOfCustomer($id_KH);
    	for ($i=0; $i < count($getCartOfCustomer); $i++) { 
    		$getTopping = $this->loginService->getToppingCart($getCartOfCustomer[$i]->ma_gio_hang);
    		$getCartOfCustomer[$i]->topping = $getTopping;
    	}
    	$getInfo =  $this->loginService->getInfoCustomer($id_KH);
    	return response()->json(['status' => 'Success', 'erro' => 0, 'Cart' => $getCartOfCustomer]);
    }

    public function rule() {
    	return view('rule.rule');
    }


    public function getEvaluate(Request $request) {
    	$ma_san_pham = $request->get('ma_san_pham');
    	$ma_kh = $request->get('ma_kh');
    	$page = $request->get('page');
    	$so_diem = $request->get('so_diem');
    	$thoi_gian = $request->get('thoi_gian');
    	$refresh =  $request->get('refresh');

    	$Evaluate = ['Vote'=>null, 'ListEv'=>null, 'list_thank'=>null, 'ListImg'=>null];
    	$vote = ['tong'=>null, 'namdiem'=>null, 'bondiem'=>null, 'badiem'=>null, 'haidiem'=>null, 'motdiem'=>null];
    	$getEvaluateOfCustomer = [];
    	$getEvaluateOfCustomerTemp = $this->loginService->getThankYouOfCustomer($ma_kh);
    	for ($i=0; $i < count($getEvaluateOfCustomerTemp); $i++) { 
    		array_push($getEvaluateOfCustomer, (string) $getEvaluateOfCustomerTemp[$i]->ma_danh_gia);
    	}
    	$getImg = [];
    	$getImgTemp = $this->loginService->getImg($ma_san_pham);
    	for ($i=0; $i < count($getImgTemp); $i++) { 
    		array_push($getImg, $getImgTemp[$i]->url);
    	}
    	$total = $this->loginService->getEvaluate($ma_san_pham);
    	$getEvaluate5 = $this->loginService->getEvaluate5($ma_san_pham);
    	$getEvaluate4 = $this->loginService->getEvaluate4($ma_san_pham);
    	$getEvaluate3 = $this->loginService->getEvaluate3($ma_san_pham);
    	$getEvaluate2 = $this->loginService->getEvaluate2($ma_san_pham);
    	$getEvaluate1 = $this->loginService->getEvaluate1($ma_san_pham);
    	$vote['tong'] = $total;
    	$vote['namdiem'] = $getEvaluate5;
    	$vote['bondiem'] = $getEvaluate4;
    	$vote['badiem'] = $getEvaluate3;
    	$vote['haidiem'] = $getEvaluate2;
    	$vote['motdiem'] = $getEvaluate1;
    	$getImgEv = [];
    	$getlist = $this->loginService->getlistEvaluate($ma_san_pham, $page, $so_diem , $thoi_gian);
    	$getlistEv = [];
    	for ($i=0; $i < count($getlist); $i++) { 
    		array_push($getlistEv, $getlist[$i]);
    	}
    	for ($i=0; $i < count($getlist); $i++) { 
    		$getThanhks = $this->loginService->getThanhks($getlist[$i]->ma_danh_gia);
    		$getImgEvTemp = $this->loginService->getImgEV($getlist[$i]->ma_danh_gia);
    		$getImgEv = [];
    		for ($g=0; $g < count($getImgEvTemp); $g++) {
    			array_push($getImgEv, $getImgEvTemp[$g]->url);
    		}
    		$listChild = $this->loginService->listChild($getlist[$i]->ma_danh_gia);
    		$getlist[$i]->so_cam_on = $getThanhks;
    		$getlist[$i]->Hinh_anh = $getImgEv;
    		$getlist[$i]->danh_gia_con = $listChild;
    	}
    	if ($page != null && $page != '') {
    		$Evaluate['ListEv'] = $getlistEv;
    		if($refresh != null){
    			$Evaluate['Vote'] = $vote;
    		}
    		return response()->json(['status' => 'Success','obj' =>  $Evaluate]);
    	}
    	$Evaluate['Vote'] = $vote;
    	$Evaluate['ListEv'] = $getlistEv;
    	$Evaluate['list_thank'] = $getEvaluateOfCustomer;
    	$Evaluate['ListImg'] = $getImg;
    	return response()->json(['status' => 'Success','obj' => $Evaluate]);

    }

    public function getChildEvaluate(Request $request) {
    	$ma_danh_gia = $request->get('ma_danh_gia');
    	$page = $request->get('page');
    	$list = [];
    	$getChildEvaluate = $this->loginService->getChildEvaluate($ma_danh_gia, $page);
    	for ($i=0; $i < count($getChildEvaluate); $i++) { 
    		array_push($list, $getChildEvaluate[$i]);
    	}
    	return response()->json(['status' => 'Success', 'error' => 0,'list' =>  $list]);

    }

    public function productDetail(Request $request) {
    	$id_SP = $request->get('id_SP');
    	$getInfoProduct = $this->loginService->getInfoProduct($id_SP);
    	$getImg = $this->loginService->getImg($getInfoProduct[0]->ma_so);
    	$getInfoProduct[0]->Arr_img = $getImg;
    	$getDanhGia = $this->loginService->getDanhGia($getInfoProduct[0]->ma_so);
    	$getInfoProduct[0]->danh_gia = $getDanhGia;
    	for ($i=0; $i < count($getDanhGia); $i++) { 
    		$getCamOn = $this->loginService->getCamOn($getDanhGia[$i]->ma_danh_gia);
    		$getDanhGia[$i]->So_danh_gia = $getCamOn;
    	}
    	return response()->json(['status' => 'Success', 'Detail' =>  $getInfoProduct]);

    }

    public function getBranch() {
    	$getPlace = $this->loginService->getPlace();
    	for ($i=0; $i < count($getPlace); $i++) { 
    		$id_place = $getPlace[$i]->ma_khu_vuc;
    		$getBranch = $this->loginService->getBranch($id_place);
    		$getPlace[$i]->Place = $getBranch;
    	}
    	return response()->json(['status' => 'Success', 'error' => 0,'Branch' =>  $getPlace]);
    }

    public function addEvaluate(Request $request) {
    	$id_tk = $request->get('id_tk');
    	$id_sp = $request->get('ma_san_pham');
    	$so_diem = $request->get('so_diem');
    	$tieu_de = $request->get('tieu_de'); 
    	$noi_dung = $request->get('noi_dung');
    	$thoi_gian = $request->get('thoi_gian');
    	$duyet = $request->get('duyet');
    	$mang_hinh = $request->get('Hinh_anh');
    	$addEvaluate = $this->loginService->addEvaluate($id_tk, $id_sp, $so_diem, $tieu_de, $noi_dung, $thoi_gian , $duyet);
    	$getIdMaxEV = $this->loginService->getIdMaxEV();
    	
    	$i = 0;
    	if(count($mang_hinh) > 0){
    		for( ; $i < count($mang_hinh) ; $i++){
	    		$insertImg = $this->loginService->insertImg($getIdMaxEV, $mang_hinh[$i]);
	    		if($insertImg == 0)
	    			break;
    		}
    	}

    	if(count($mang_hinh) == $i)	
	    	if ($addEvaluate == true) {
	    		return response()->json(['status' => 'Success','error' =>  0]);
	    	}
    	return response()->json(['status' => 'Fail','error' =>  1]);
    }

    public function addChildEvaluate(Request $request) {
    	$id_Evaluate = $request->get('ma_danh_gia');
    	$id_tk = $request->get('ma_tk');
    	$noi_dung = $request->get('noi_dung');
    	$thoi_gian = $request->get('thoi_gian');
    	$duyet = $request->get('duyet');
    	$addChildEvaluate = $this->loginService->addChildEvaluate($id_Evaluate, $id_tk, $noi_dung, $thoi_gian , $duyet);
    	if ($addChildEvaluate == true) {
    		return response()->json(['status' => 'Success','error' =>  0]);
    	}
    	return response()->json(['status' => 'Fail','error' =>  1]);
    }

    public function addThanks(Request $request) {
    	$id_Evaluate = $request->get('id_Evaluate');
    	$id_KH = $request->get('id_KH');
    	$addThanks = $this->loginService->addThanks($id_Evaluate, $id_KH);
    	if ($addThanks == true) {
    		return response()->json(['status' => 'Success','error' =>  0]);
    	}
    	return response()->json(['status' => 'Fail','error' =>  1]);
    }

    public function getOrderDetail(Request $request) {
    	$ma_don_hang = $request->get('ma_don_hang');
    	$getDetail = $this->loginService->getDetail($ma_don_hang);
    	for ($j=0; $j < count($getDetail); $j++) { 
					$getTopping = $getTopping = $this->loginService->getTopping($getDetail[$j]->ma_chi_tiet);
					$getDetail[$j]->topping = $getTopping;
			}	
    	return response()->json(['status' => 'Success','error' =>  0, 'Detail' => $getDetail]);
    }

	public function getAddressOrderUser(Request $request) {
    	$account = $request->get('ma_tai_khoan');
    	$main = $request->get('main');
    	$listInfoAddress = $this->loginService->getAddressOrderUser($account , $main);
    	return response()->json(['status' => 'Success','error' =>  0, 'listAddress' => $listInfoAddress]);
	}

	public function insertAddresOrderUser(Request $request){
    	$ma_tai_khoan = $request->get('ma_tai_khoan');
    	$ten_nguoi_nhan = $request->get('ten_nguoi_nhan');
    	$dia_chi = $request->get('dia_chi');
    	$so_dien_thoai = $request->get('so_dien_thoai');
    	$chinh = $request->get('chinh');

    	$resultInsert = $this->loginService->insertAddressOrderUser($ma_tai_khoan , $ten_nguoi_nhan , $dia_chi , $so_dien_thoai , $chinh);

    	if($resultInsert > 0)
    		return response()->json(['status' => 'Success','error' =>  0]);
    	else
    		return response()->json(['status' => 'fail','error' =>  1]);
	}

	public function updateAddresOrderUser(Request $request){
		
		$id = $request->get('id');
    	$ten_nguoi_nhan = $request->get('ten_nguoi_nhan');
    	$so_dien_thoai = $request->get('so_dien_thoai');
    	$dia_chi = $request->get('dia_chi');
    	$chinh = $request->get('chinh');
    	$da_xoa = $request->get('da_xoa');

    	$resultUpdate = $this->loginService->updateAddressOrderUser($id , $ten_nguoi_nhan , $dia_chi , $so_dien_thoai , $chinh , $da_xoa);
    	if($resultUpdate > 0)
    		return response()->json(['status' => 'Success','error' =>  0]);
    	else
    		return response()->json(['status' => 'fail','error' =>  1]);
	}
	
    public function getChildImage(Request $request) {
    	$ma_sp = $request->get('ma_san_pham');
    	$listImg = $this->loginService->getImg($ma_sp);
    	return response()->json(['status' => 'Success','error' =>  0, 'listImage' => $listImg]);
    }

    public function getThankYouOfCustomer(Request $request) {
    	$ma_kh = $request->get('ma_kh');
    	$getEvaluateOfCustomer = $this->loginService->getThankYouOfCustomer($ma_kh);
    	return response()->json(['status' => 'Success','error' =>  0, 'list' => $getEvaluateOfCustomer]);
    }

    public function getQuantityAndPrice(Request $request) {
    	$ma_kh = $request->get('ma_kh');
    	$TotalCart = 0;
    	$getTotalQuantity = $this->loginService->getQuantityAndPrice($ma_kh);
    	$getSp = $this->loginService->getSp($ma_kh);
    	for ($i=0; $i < count($getSp); $i++) {
    		$getSLTP = $this->loginService->getSLTP($getSp[$i]->ma_gio_hang);
    		for ($y=0; $y < count($getSLTP); $y++) { 
    			$PriceTopping = $getSLTP[$y]->gia_san_pham*$getSLTP[$y]->so_luong;
    			$getSLTP[$y]->total = $PriceTopping;
    		}
    		$totalTopping = 0;
    		for ($e=0; $e < count($getSLTP); $e++) { 
    			$totalTopping += $getSLTP[$e]->total;
    		}
    		if ($getSp[$i]->kich_co == 'L') {
    			$getSL = $this->loginService->getSLSP($getSp[$i]->ma_gio_hang, $getSp[$i]->kich_co);
    			for ($z=0; $z < count($getSL); $z++) { 
    				$PriceSP = $getSL[$z]->gia_lon;
    				$getSL[$z]->gia_lon = $PriceSP;
    				$getSp[$i]->gia_lon = $getSL[$z]->gia_lon;
    			}
    			$getSp[$i]->totalTopping = $totalTopping;
    			$getSp[$i]->Total = ($getSp[$i]->totalTopping + $getSp[$i]->gia_lon)*$getSp[$i]->so_luong;
    			$TotalCart += $getSp[$i]->Total;
    		}
    		if ($getSp[$i]->kich_co == 'S') {
    			$getSL = $this->loginService->getSLSP($getSp[$i]->ma_gio_hang, $getSp[$i]->kich_co);
    			for ($z=0; $z < count($getSL); $z++) { 
    				$PriceSP = $getSL[$z]->gia_san_pham;
    				$getSL[$z]->gia_lon = $PriceSP;
    				$getSp[$i]->gia_lon = $getSL[$z]->gia_lon;
    			}
    			$getSp[$i]->totalTopping = $totalTopping;
    			$getSp[$i]->Total = ($getSp[$i]->totalTopping + $getSp[$i]->gia_lon)*$getSp[$i]->so_luong;
    			$TotalCart += $getSp[$i]->Total;
    		}
    		if ($getSp[$i]->kich_co == 'M') {
    			$getSL = $this->loginService->getSLSP($getSp[$i]->ma_gio_hang, $getSp[$i]->kich_co);
    			for ($z=0; $z < count($getSL); $z++) { 
    				$PriceSP = $getSL[$z]->gia_vua;
    				$getSL[$z]->gia_lon = $PriceSP;
    				$getSp[$i]->gia_lon = $getSL[$z]->gia_lon;
    			}
    			$getSp[$i]->totalTopping = $totalTopping;
    			$getSp[$i]->Total = ($getSp[$i]->totalTopping + $getSp[$i]->gia_lon)*$getSp[$i]->so_luong;
    			$TotalCart += $getSp[$i]->Total;
    		}
    	}
    	
    	return response()->json(['status' => 'Success','error' =>  0, 
    		'TotalCart' => (int)$TotalCart , 'TotalQuantity' => $getTotalQuantity]);
    }

    public function updatePassword(Request $request){
    	$idUser = $request->get('ma_kh');
    	$password = $request->get('password');

    	$result = $this->loginService->updatePassword($idUser , $password);

    	if($result > 0)
    		return response()->json(['status' => 'Success','error' =>  0]);
    	else
    		return response()->json(['status' => 'error','error' =>  0]);
    }

    public function updateNumberPhone(Request $request){
		$idUser = $request->get('ma_kh');
    	$phone = $request->get('phone');

    	$result = $this->loginService->updateNumberPhone($idUser , $phone);

    	if($result > 0)
    		return response()->json(['status' => 'Success','error' =>  0]);
    	else
    		return response()->json(['status' => 'error','error' =>  0]);
    }

    public function getSlideShow(Request $request){
    	$slide = $request->get('slide');
    	$getSlideShow = $this->loginService->getSlideShow($slide);
    	for($i=0; $i < count($getSlideShow); $i ++) {
    		$totalDiscountOfOrder = $this->loginService->totalDiscountOfOrder($getSlideShow[$i]->ma_khuyen_mai);

    		if($getSlideShow[$i]->gioi_han_so_code != null)
    			$getSlideShow[$i]->ma_con_lai = $getSlideShow[$i]->gioi_han_so_code - count($totalDiscountOfOrder);
    	}
		return response()->json(['status' => 'Success', 'error' =>  0, 'listSlide' => $getSlideShow]);
	}

	public function getAllLogPointUser(Request $request){
		$idUser = $request->get('id_KH');
    	$listLog = $this->loginService->getAllLogPointUser($idUser);
    	return response()->json(['status' => 'Success', 'error' =>  0, 'listPoint' => $listLog]);
	}
}
