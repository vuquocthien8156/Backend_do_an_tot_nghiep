<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\AccountRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class AccountService {
	protected $loginRepository;

	public function __construct(AccountRepository $accountRepository) {
		$this->accountRepository = $accountRepository;
	}

	public function search($name, $phone, $page, $gender) {
		return $this->accountRepository->search($name, $phone, $page, $gender);
	}

	public function delete($id) {
		return $this->accountRepository->delete($id);
	}

	public function editUser($avatar_path, $ten, $id, $SDT, $NS, $gender, $diemtich, $diachi, $email, $now) {
		return $this->accountRepository->editUser($avatar_path, $ten, $id, $SDT, $NS, $gender, $diemtich, $diachi, $email, $now);
	}
}