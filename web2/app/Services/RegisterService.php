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

	public function getAccount() {
		return $this->registerRepository->getAccount();
	}

	public function insertUser($username, $password, $name, $gender, $birthday, $phone, $address) {
		return $this->registerRepository->insertUser($username, $password, $name, $gender, $birthday, $phone, $address);
	}
	public function idMax() {
		return $this->registerRepository->idMax();
	}

	public function insertPermission($idMax) {
		return $this->registerRepository->insertPermission($idMax);
	}
}