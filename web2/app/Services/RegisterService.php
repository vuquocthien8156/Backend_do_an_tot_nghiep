<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\RegisterRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class RegisterService {
	protected $loginRepository;

	public function __construct(RegisterRepository $registerRepository) {
		$this->registerRepository = $registerRepository;
	}
	
	public function login($user, $pass) {
		return $this->registerRepository->login($user, $pass);
	}

	public function getAccount($username) {
		return $this->registerRepository->getAccount($username);
	}

	public function getPhone($username) {
		return $this->registerRepository->getPhone($username);
	}

	public function insertUser($username, $password, $name, $gender, $birthday, $phone, $address) {
		return $this->registerRepository->insertUser($username, $password, $name, $gender, $birthday, $phone, $address);
	}

	public function insertUserPhone($username, $password, $name, $gender, $birthday, $address) {
		return $this->registerRepository->insertUserPhone($username, $password, $name, $gender, $birthday, $address);
	}

	public function idMax() {
		return $this->registerRepository->idMax();
	}

	public function insertPermission($idMax) {
		return $this->registerRepository->insertPermission($idMax);
	}
}