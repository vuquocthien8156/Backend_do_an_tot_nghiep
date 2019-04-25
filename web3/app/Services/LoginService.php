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

}