<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\ConfigRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;
use App\Repositories\EmployeesRepository;

class EmployeesService {
    protected $employeesRepository;

	public function __construct(EmployeesRepository $employeesRepository) {
		$this->employeesRepository = $employeesRepository;
    }

    public function saveEmployees($name, $phone, $email, $avatar_path, $type_employees, $created_by) {
        return $this->employeesRepository->saveEmployees($name, $phone, $email, $avatar_path, $type_employees, $created_by);
    }

    public function saveStaffBranch($branch_id, $staff_id, $created_by) {
        return $this->employeesRepository->saveStaffBranch($branch_id, $staff_id, $created_by);
    }

    public function updateEmployees($id_user, $name, $email, $avatar_path, $category_id_edit, $update_by, $now, $birthday_edit) {
        return $this->employeesRepository->updateEmployees($id_user, $name, $email, $avatar_path, $category_id_edit, $update_by, $now, $birthday_edit);
    }

    public function updateBranchStaff($branch_id, $id_user, $update_by, $updated_at) {
        return $this->employeesRepository->updateBranchStaff($branch_id, $id_user, $update_by, $updated_at);
    }

    public function deleteEmployees($id_user) {
        return $this->employeesRepository->deleteEmployees($id_user);
    }

    public function searchEmployees($branch_id, $name_phone_email, $status, $type_employees) {
        return $this->employeesRepository->searchEmployees($branch_id, $name_phone_email, $status, $type_employees);
    }

    public function getStaffType() {
        return $this->employeesRepository->getStaffType();
    }

    public function deleteEmployeesUser($id_user) {
        return $this->employeesRepository->deleteEmployeesUser($id_user);
    }
}