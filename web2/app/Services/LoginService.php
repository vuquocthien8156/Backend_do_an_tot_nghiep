<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\LoginRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class LoginService {
	protected $loginRepository;

	public function __construct(LoginRepository $loginRepository) {
		$this->loginRepository = $loginRepository;
	}
	
	public function login($user, $pass) {
		return $this->loginRepository->login($user, $pass);
	}

	public function getInfoByEmail($email) {
		return $this->loginRepository->getInfoByEmail($email);
	}

	public function loginsdt($user) {
		return $this->loginRepository->loginsdt($user);
	}

	public function check($user) {
		return $this->loginRepository->check($user);
	}

	public function idMax() {
		return $this->loginRepository->idMax();
	}
		
	public function updateInfo($email, $name, $phone, $gender, $dob, $avatar, $id) {
		return $this->loginRepository->updateInfo($email, $name, $phone, $gender, $dob, $avatar, $id);	
	}

	public function getLikedProduct($id) {
		return $this->loginRepository->getLikedProduct($id);	
	}

	public function getLike() {
		return $this->loginRepository->getLike();	
	}

	public function updateLike($id_product, $id_user, $like) {
		return $this->loginRepository->updateLike($id_product, $id_user, $like);	
	}

	public function insertLike($id_product, $id_user, $like) {
		return $this->loginRepository->insertLike($id_product, $id_user, $like);	
	}

	public function getAllOrder($id_KH) {
		return $this->loginRepository->getAllOrder($id_KH);
	}

	public function getUser($id_KH) {
		return $this->loginRepository->getUser($id_KH);
	}

	public function getUser2($id_KH) {
		return $this->loginRepository->getUser2($id_KH);
	}

	public function getDetailOrder($id_don_hang) {
		return $this->loginRepository->getDetailOrder($id_don_hang);
	}

	public function updateUserFB($id_fb, $email , $type) {
		return $this->loginRepository->updateUserFB($id_fb, $email , $type);
	}

	public function getInfo($id_fb) {
		return $this->loginRepository->getInfo($id_fb);
	}

	public function loginfb($id_fb) {
		return $this->loginRepository->loginfb($id_fb);
	}
	
	public function create($id_fb, $email , $name) {
		return $this->loginRepository->create($id_fb, $email  , $name);
	}

	public function news($page) {
		return $this->loginRepository->news($page);
	}
	
	public function productType($page) {
		return $this->loginRepository->productType($page);
	}

	public function getCart() {
		return $this->loginRepository->getCart();
	}

	public function deleteCart($id_GH) {
		return $this->loginRepository->deleteCart($id_GH);
	}

	public function deleteCartCustomer($id_KH) {
		return $this->loginRepository->deleteCartCustomer($id_KH);
	}

	public function getQuantity($id_GH) {
		return $this->loginRepository->getQuantity($id_GH);
	}

	public function updateCart($ma_gio_hang, $ma_topping, $ten_topping, $so_luong_topping) {
		return $this->loginRepository->updateCart($ma_gio_hang, $ma_topping, $ten_topping, $so_luong_topping);
	}

	public function getCartOfCustomer($id_KH, $id_GH) {
		return $this->loginRepository->getCartOfCustomer($id_KH, $id_GH);
	}

	public function getInfoCustomer($id_KH) {
		return $this->loginRepository->getInfoCustomer($id_KH);
	}

	public function getEvaluate() {
    	return $this->loginRepository->getEvaluate();
    }

    public function getChildEvaluate($id_SP, $id_Evaluate) {
    	return $this->loginRepository->getChildEvaluate($id_SP, $id_Evaluate);
    }

    public function getEvaluateOfProduct($id_SP) {
    	return $this->loginRepository->getEvaluateOfProduct($id_SP);
    }

    public function getPlace() {
    	return $this->loginRepository->getPlace();
    }

    public function getBranch($id_place) {
    	return $this->loginRepository->getBranch($id_place);
    }

    public function addEvaluate($id_tk, $id_sp, $so_diem, $tieu_de, $noi_dung, $thoi_gian, $hinh_anh, $parent_id){
    	return $this->loginRepository->addEvaluate($id_tk, $id_sp, $so_diem, $tieu_de, $noi_dung, $thoi_gian, $hinh_anh, $parent_id);
    }

    public function addThanks($id_Evaluate, $id_KH) {
    	return $this->loginRepository->addThanks($id_Evaluate, $id_KH);
    }


    public function insertTopping($ma_gio_hang, $topping) {
    	return $this->loginRepository->insertTopping($ma_gio_hang, $topping);
    }

    public function getTopping($ma_chi_tiet) {
    	return $this->loginRepository->getTopping($ma_chi_tiet);
    }

    public function getToppingCart($ma_gio_hang) {
    	return $this->loginRepository->getToppingCart($ma_gio_hang);
    }

    public function getStatusOrder($ma_don_hang) {
    	return $this->loginRepository->getStatusOrder($ma_don_hang);
    }

    public function getDetail($ma_don_hang) {
    	return $this->loginRepository->getDetail($ma_don_hang);
    }

    public function selectMaxId() {
    	return $this->loginRepository->selectMaxId();
    }

    public function getDetailCart($ma_gio_hang) {
    	return $this->loginRepository->getDetailCart($ma_gio_hang);
    }

    public function getOrderDetail($ma_don_hang) {
    	return $this->loginRepository->getOrderDetail($ma_don_hang);
    }

    public function getInfoProduct($id_SP) {
    	return $this->loginRepository->getInfoProduct($id_SP);	
    }

    public function getImg($ma_sp) {
    	return $this->loginRepository->getImg($ma_sp);
    }

    public function getDanhGia($ma_so) {
    	return $this->loginRepository->getDanhGia($ma_so);
    }

    public function getCamOn($ma_DG) {
    	return $this->loginRepository->getCamOn($ma_DG);
    }

    public function insertCart($idCustomer, $ma_sp, $so_luong, $size, $note) {
    	return $this->loginRepository->insertCart($idCustomer, $ma_sp, $so_luong, $size, $note);
    }

    public function getSoLuong($ma_san_pham) {
    	return $this->loginRepository->getSoLuong($ma_san_pham);
    }

    public function updateTopping($ma_san_pham, $sl) {
    	return $this->loginRepository->updateTopping($ma_san_pham, $sl);
    }

    public function updateCartOfCustomer($idCustomer, $idCart, $ma_sp, $so_luong, $size, $note) {
    	return $this->loginRepository->updateCartOfCustomer($idCustomer, $idCart, $ma_sp, $so_luong, $size, $note);
    }

    public function deleteToppingOfCart($idCart) {
    	return $this->loginRepository->deleteToppingOfCart($idCart);
    }

    public function getIdImg() {
    	return $this->loginRepository->getIdImg();
    }
}