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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Excel;

class LoginController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(LoginService $loginService) {
		$this->loginService = $loginService;
	}

	public function loginView(Request $request) {
		if ($request->session()->has('name') == false) {
	  		return view('login.login2');
	  	}
	  	else {
	  		return redirect()->route('home');
	  	}  
	}
	
	public function check(Request $request) {
		$user = $request->get("username");
		$pass = $request->get("password");
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
	if ($request->session()->has('name') == true) {
	  		return redirect()->route('home');
	  	}  
		$user = $request->get("username");
		$pass = md5($request->get("password"));
		$check = $this->loginService->login($user, $pass);
		if (isset($check[0]->user_id)) {
			session()->put('id',$check[0]->user_id);
			session()->put('name',$check[0]->ten);
			session()->put('login',true);
			session()->put('vaitro',$check[0]->id_vai_tro);
			session()->put('quyen_he_thong',$check[0]->quyen_he_thong);
			return response()->json(['status' => 'ok', 'error' => 0, $check]);
		}
		else
		{
			return response()->json(['status' => 'error','error' => 1]);
		}
	}

	public function loginAPI(Request $request) {  
		$user = $request->get("username");
		$pass = md5($request->get("password"));
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
		return redirect('login');
	}

	public function twoDigitNumber($number) {
		return $number < 10 ? '0'.$number : $number;
    }
	
	public function uploadImage(Request $request){
		// $now = Carbon::now();
		// $getIdImg = $this->loginService->getIdImg()+1;
		// if ($request->file('avatar') != null || $request->file('avatar') != ''){
	 //            $subName = 'account/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
	 //            $destinationPath = config('app.resource_physical_path');
	 //            $pathToResource = config('app.resource_url_path');
	 //            $nameImg = 'Account_Img'.$getIdImg.strstr($request->file('avatar')->getClientOriginalName(), '.');
	 //            // $filename =  $subName . '/'. $nameImg->getClientOriginalName();
	 //            $check = $request->file('avatar')->move($destinationPath.'/'.$subName, $nameImg);
  //           	if (!file_exists($check)) {
  //               	return response()->json(['status' => 'null']);
  //           	}
  //           return response()->json(['filename' => 'images/' . $nameImg]);
		
		$now = Carbon::now();
		$a = Hash::make(1);
		if ($request->file('avatar') != null || $request->file('avatar') != ''){
	            $subName = 'account/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
	            $destinationPath = config('app.resource_physical_path');
	            $pathToResource = config('app.resource_url_path');
	            $filename =  $subName . '/'.$a. $request->file('avatar')->getClientOriginalName();
	            $check = $request->file('avatar')->move($destinationPath.'/'.$subName, $filename);
            	if (!file_exists($check)) {
                	return response()->json(['status' => 'null']);
            	}
            return response()->json(['filename' => 'images/' . $filename]);
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
		if (isset($getLikedProduct[0]->ma_chu)) {
			return response()->json(['status' => 'ok', 'error' => 0, 'list' => $getLikedProduct]);
		}else{
			return response()->json(['status' => 'fail', 'error' => 1]);
		}
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
		return response()->json(['status' => 'Success', 'error' => 0, 'listSearch'=>$list]);
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
    	$ma_sp = $objectCart['idProduct'];
    	$so_luong = $objectCart['quantity'];
    	$size = $objectCart['size'];
    	$note = $objectCart['note'];

    	$topping = $objectCart['topping'];
    	$getCart = $this->loginService->getCart();
    	$check = false;
    	$updateTopping = 0;
	    for ($i=0; $i < count($getCart); $i++) { 
	    	if ($idCustomer == $getCart[$i]->ma_khach_hang && $ma_sp == $getCart[$i]->ma_san_pham && $size == $getCart[$i]->kich_co) {
	    		$a = $i;
	    		$check = true;
	    		break;
	    	}else {
	    		$check = false;
	    	}
	    }

	    if ($check == true) {
	    	$getDetailCart = $this->loginService->getDetailCart($getCart[$a]->ma_gio_hang);
	    	for ($y=0; $y < count($getDetailCart); $y++) {
	    		for ($k=0; $k < count($topping); $k++) { 
	    		 	if ($topping[$k]['idProduct'] == $getDetailCart[$y]->ma_san_pham) {
	    				$getSoLuong = $this->loginService->getSoLuong($getDetailCart[$y]->ma_san_pham);
	    				$sl = $getSoLuong[0]->so_luong + $topping[$k]['quantity'];
						$updateTopping = $this->loginService->updateTopping($getDetailCart[$y]->ma_san_pham, $sl);
	    			}
	    		}
	    	}
	    	if ($updateTopping == 1) {
				return response()->json(['status' => 'Success', 'error' => 0]);
			}else {
				$insertCart = $this->loginService->insertCart($idCustomer, $ma_sp, $so_luong, $size, $note);
				$selectMaxId = $this->loginService->selectMaxId();
				$insertTopping = $this->loginService->insertTopping($selectMaxId, $topping);
				if ($insertCart == true && $insertTopping == true) {
						return response()->json(['status' => 'Success', 'error' => 0]);
					}
				return response()->json(['status' => 'fail', 'error' => 1]);
			}
	    }else {
	    	$insertCart = $this->loginService->insertCart($idCustomer, $ma_sp, $so_luong, $size, $note);
			$selectMaxId = $this->loginService->selectMaxId();
			$insertTopping = $this->loginService->insertTopping($selectMaxId, $topping);
			if ($insertCart == true && $insertTopping == true) {
				return response()->json(['status' => 'Success', 'error' => 0]);
			}
			return response()->json(['status' => 'fail', 'error' => 1]);
	    }
    }

    public function updateCart (Request $request) {
    	$idCustomer = $request->get('idCustomer');
    	$idCart = $request->get('idCart');
    	$objectCart = $request->get('cart');
    	$ma_sp = $objectCart['idProduct'];
    	$so_luong = $objectCart['quantity'];
    	$size = $objectCart['size'];
    	$note = $objectCart['note'];

    	$topping = $objectCart['topping'];
    	$updateCartOfCustomer = $this->loginService->updateCartOfCustomer($idCustomer, $idCart, $ma_sp, $so_luong, $size, $note);
    	$deleteToppingOfCart = $this->loginService->deleteToppingOfCart($idCart);
    	$insertTopping = $this->loginService->insertTopping($idCart, $topping);
    	if ($updateCartOfCustomer == 1 && $insertTopping == true) {
    		return response()->json(['status' => 'Success', 'error' => 0]);
    	}
    	return response()->json(['status' => 'fail', 'error' => 1]);
    }

    public function deleteCart(Request $request) {
    	$id_GH = $request->get('id_GH');
    	$deleteCart = $this->loginService->deleteCart($id_GH);
    	if ($deleteCart == 1) {
    		return response()->json(['status' => 'Success', 'error' => 0]);
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
    	$id_GH = $request->get('id_GH');
    	$getCartOfCustomer = $this->loginService->getCartOfCustomer($id_KH, $id_GH);
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

    public function getEvaluate() {
    	$getEvaluate = $this->loginService->getEvaluate();
    	return response()->json(['status' => 'Success','list' =>  $getEvaluate]);

    }

    public function getChildEvaluate(Request $request) {
    	$id_SP = $request->get('id_SP');
    	$id_Evaluate = $request->get('id_Evaluate');
    	$getChildEvaluate = $this->loginService->getChildEvaluate($id_SP, $id_Evaluate);
    	return response()->json(['status' => 'Success','list' =>  $getChildEvaluate]);

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
    	return response()->json(['status' => 'Success','Branch' =>  $getPlace]);
    }

    public function addEvaluate(Request $request) {
    	$id_tk = $request->get('id_tk');
    	$id_sp = $request->get('id_sp');
    	$so_diem = $request->get('so_diem');
    	$tieu_de = $request->get('tieu_de'); 
    	$noi_dung = $request->get('noi_dung');
    	$thoi_gian = $request->get('thoi_gian');
    	$hinh_anh = $request->get('hinh_anh');
    	$parent_id = $request->get('parent_id');
    	if ($request->file('hinh_anh') != null || $request->file('hinh_anh') != '') {
                $subName = 'user/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename =  $subName . '/' . $request->file('hinh_anh')->getClientOriginalName();
                $check = $request->file('hinh_anh')->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $hinh_anh = $filename;
        }
    	$addEvaluate = $this->loginService->addEvaluate($id_tk, $id_sp, $so_diem, $tieu_de, $noi_dung, $thoi_gian, $hinh_anh, $parent_id);
    	if ($addEvaluate == true) {
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
}
