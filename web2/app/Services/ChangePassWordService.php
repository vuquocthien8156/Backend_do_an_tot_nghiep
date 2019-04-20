<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\ChangePassWordRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;

class ChangePassWordService {
    protected $changePassWordRepository;

	public function __construct(ChangePassWordRepository $changePassWordRepository) {
		$this->changePassWordRepository = $changePassWordRepository;
    }

    public function changepassword($id_user, $password, $name) {
        return $this->changePassWordRepository->changepassword($id_user, $password, $name);
    }
}