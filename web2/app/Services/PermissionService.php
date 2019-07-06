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

	public function getListPermission() {
		return $this->permissionRepository->getListPermission();
	}

	public function getListInternalUser() {
		return $this->permissionRepository->getListInternalUser();
	}

	public function Permission($id_per, $id_user) {
		return $this->permissionRepository->Permission($id_per, $id_user);
	}

	public function listPermissionUser() {
		return $this->permissionRepository->listPermissionUser();
	}

	public function inserPermission($user_id, $permission_group) {
		return $this->permissionRepository->inserPermission($user_id, $permission_group);
	}

	public function inserUser($name, $phone, $email, $password, $permission_group) {
		return $this->permissionRepository->inserUser($name, $phone, $email, $password, $permission_group);
	}

	public function getRoll($roll) {
		return $this->permissionRepository->getRoll($roll);
	}

	public function getNamePer($roll) {
		return $this->permissionRepository->getNamePer($roll);
	}	

	public function getMaxId() {
		return $this->permissionRepository->getMaxId();
	}

	public function deletePermission($user_id,$status) {
		return $this->permissionRepository->deletePermission($user_id,$status);
	}
}