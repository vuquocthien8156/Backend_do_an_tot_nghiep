<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\PermissionRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class PermissionService {
	protected $loginRepository;

	public function __construct(PermissionRepository $permissionRepository) {
		$this->permissionRepository = $permissionRepository;
	}

}