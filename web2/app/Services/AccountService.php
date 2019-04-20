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

	public function search($name, $page = 1, $pageSize = 15) {
		return $this->accountRepository->search($name, $page = 1, $pageSize = 15);
	}

	public function delete($id) {
		return $this->accountRepository->delete($id);
	}
}