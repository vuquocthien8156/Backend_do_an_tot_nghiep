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
		return redirect('home');
	}

	public function twoDigitNumber($number) {
		return $number < 10 ? '0'.$number : $number;
    }
	
	public function uploadImage(Request $request){
		$now = Carbon::now();
		if ($request->file('avatar') != null || $request->file('avatar') != ''){
	            $subName = 'images/account/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);
	            $destinationPath = config('app.resource_physical_path');
	            $pathToResource = config('app.resource_url_path');
	            $filename =  $subName . '/' . $request->file('avatar')->getClientOriginalName();
	            $check = $request->file('avatar')->move($destinationPath.'/'.$subName, $filename);
            	if (!file_exists($check)) {
                	return 'fail';
            	}
            return $filename;
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
		$id_KH = $request->get('id_khach_hang');
		if ($id_KH == '' || $id_KH == null) {
			$getAllOrder =  null;
			$getUser = null;
		}
		else
		{
			$getAllOrder =  $this->loginService->getAllOrder($id_KH);
			$getUser = $this->loginService->getUser($id_KH);
			$id_don_hang = $getAllOrder[0]->ma_don_hang;
			//$getDetailOrder = $this->loginService->getDetailOrder($id_don_hang);
			for ($i=0; $i < count($getAllOrder) ; $i++) { 
				$getAllOrder[$i]->ma_khach_hang = $getUser[0];
			}
		}
		dd($getAllOrder, $id_don_hang);
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
    	$objectCart = $request->get('object');
    	$id_KH = $objectCart['id'];
    	$list = $objectCart['list'];
    	for ($i=0; $i < count($list); $i++) { 
    		$id_sp = $list[$i]['ma_san_pham'];
    		$so_luong = $list[$i]['so_luong'];
    		$size = $list[$i]['size'];
    		$parent_id = $list[$i]['parent_id'];
    		$insertCart = $this->loginService->insertCart($id_KH, $id_sp, $size, $so_luong, $parent_id);
    	}
    	$getCartOfCustomer = $this->loginService->getCart($id_KH);
    	if (isset($getCartOfCustomer[0]->ma_gio_hang)) {
    		return response()->json(['ma_khach_hang' => $id_KH,'list' => $getCartOfCustomer]);
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

    public function updateQuantity(Request $request) {
    	$id_GH = $request->get('id_GH');
    	$type = $request->get('type');
    	$getQuantity = $this->loginService->getQuantity($id_GH);
    	$sl = $getQuantity[0]->so_luong;
    	$updateQuantity = $this->loginService->updateQuantity($id_GH, $sl, $type);
    	if ($updateQuantity == 1) {
    		return response()->json(['status' => 'Success', 'error' => 0]);
    	}
    	return response()->json(['status' => 'fail', 'error' => 1]);
    }
}
