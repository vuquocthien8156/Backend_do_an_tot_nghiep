<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\AuthorizationRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;

class AuthorizationService {
    protected $authorizationRepository;

	public function __construct(AuthorizationRepository $authorizationRepository) {
        $this->authorizationRepository = $authorizationRepository;
    }

    public function getAuthorizationUser($id_user) {
        return $this->authorizationRepository->getAuthorizationUser($id_user);
    }

    public function getPermisstionGroup() {
        return $this->authorizationRepository->getPermisstionGroup();
    }

    public function saveAuthorizationUserWeb($name, $phone, $email, $password, $permission_group, $created_by) {
        return $this->authorizationRepository->saveAuthorizationUserWeb($name, $phone, $email, $password, $permission_group, $created_by);
    }

    public function getListAuthorizationUserWeb() {
        return $this->authorizationRepository->getAuthorizationUserWeb();
    }

    public function checkAuthorizationPage($user_id, $code_page) {
        return $this->authorizationRepository->checkAuthorizationPage($user_id, $code_page);
    }

    public function getListAccess($user_id) {
        return $this->authorizationRepository->getListAccess($user_id);
    }

    public function deleteAuthorizationUserWeb($user_id) {
        return $this->authorizationRepository->deleteAuthorizationUserWeb($user_id);
    }

    public function updateAuthorizationUserWeb($user_id, $name, $phone, $email, $permission_group, $updated_by) {
        $deletePermissionGroupOld = $this->authorizationRepository->deleteRolePermissionByUserId($user_id);
        $updateUser = $this->authorizationRepository->updateAuthorizationUserWeb($user_id, $name, $phone, $email, $updated_by);
        
        foreach ($permission_group as $key => $value) {
            $saveNewPermission = $this->authorizationRepository->saveUserRolePermission($user_id, $value);
        }
        return $saveNewPermission;
    }
}