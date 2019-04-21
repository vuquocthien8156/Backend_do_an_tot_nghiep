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

	public function getListpermission() {
		return $this->permissionRepository->getListpermission();
	}

	public function getListInternalUser() {
		return $this->permissionRepository->getListInternalUser();
	}

	public function Permission($id_per, $id_user) {
		return $this->permissionRepository->Permission($id_per, $id_user);
	}
}