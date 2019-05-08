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

	public function loginsdt($user) {
		return $this->loginRepository->loginsdt($user);
	}

	public function check($user) {
		return $this->loginRepository->check($user);
	}

	public function updateInfo($email, $name, $phone, $gender, $dob, $avatar) {
		return $this->loginRepository->updateInfo($email, $name, $phone, $gender, $dob, $avatar);	
	}

	public function getLikedProduct($email) {
		return $this->loginRepository->getLikedProduct($email);	
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

	public function updateIdFB($id_fb, $email) {
		return $this->loginRepository->updateIdFB($id_fb, $email);
	}

	public function getInfo($id_fb) {
		return $this->loginRepository->getInfo($id_fb);
	}
}