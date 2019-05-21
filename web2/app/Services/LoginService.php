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

	public function insertCart($id_KH, $id_sp, $size, $so_luong, $parent_id) {
		return $this->loginRepository->insertCart($id_KH, $id_sp, $size, $so_luong, $parent_id);
	}

	public function getCart($id_KH) {
		return $this->loginRepository->getCart($id_KH);
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

	public function updateQuantity($id_GH, $sl, $type) {
		return $this->loginRepository->updateQuantity($id_GH, $sl, $type);
	}
}