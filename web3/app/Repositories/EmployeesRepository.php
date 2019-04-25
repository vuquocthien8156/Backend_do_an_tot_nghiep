<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Models\Users;
use App\Models\Branch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\BranchStaff;

class EmployeesRepository {

	public function __construct(Users $users, BranchStaff $branchStaff) {
        $this->users = $users;
        $this->branchStaff = $branchStaff;
    }

    public function saveEmployees($name, $phone, $email, $avatar_path, $type_employees, $created_by) {
        try {
            $now = Carbon::now();
            $users = new Users();
            $users->type = EUser::TYPE_STAFF;
            $users->status = EStatus::ACTIVE;
            $users->phone = $phone;
            $users->email = $email;
            $users->name = $name;
            $users->avatar_path = $avatar_path;
            $users->created_at = $now;
            $users->created_by = $created_by;
            $users->staff_type_id = $type_employees;
            $users->save();
            return $users;
        } catch (\Exception $e) {
            logger('Fail Save employees: ' . $name . ' time:' . $now, ['e' => $e]);
            return null;
        }
    }

    public function saveStaffBranch($branch_id, $staff_id, $created_by) {
        try {
            $now = Carbon::now();
            $users = new BranchStaff();
            $users->status = EStatus::ACTIVE;
            $users->staff_id = $staff_id;
            $users->branch_id = $branch_id;
            $users->created_at = $now;
            $users->created_by = $created_by;
            $users->save();
            return $users;
        } catch (\Exception $e) {
            logger('Fail Save branch staff, branch_id: ' . $branch_id . ' time:' . $now, ['e' => $e]);
            return null;
        }
    }

    public function updateEmployees($id_user, $name, $email, $avatar_path, $category_id_edit, $update_by, $now, $birthday_edit) {
        $result = Users::where('id', '=', $id_user)
                        ->update([  'name' => $name,
                                    'email' => $email, 'avatar_path' => $avatar_path,
                                    'updated_by' => $update_by,
                                    'staff_type_id' => $category_id_edit,
                                    'updated_at' => $now,
                                    'date_of_birth' => $birthday_edit,
                                ]);
        return $result;
    }

    public function updateBranchStaff($id_user, $branch_id, $updated_by, $updated_at) {
        $result = BranchStaff::where('staff_id', '=', $id_user)
                        ->update([  'branch_id' => $branch_id,
                                    'updated_at' =>$updated_at,
                                    'updated_by' =>$updated_by]);
        return $result;
    }

    public function deleteEmployees($id_user) {
        $result = BranchStaff::where('staff_id', '=', $id_user)
                        ->update(['status' => EStatus::DELETED]);
        return $result;
    }

    public function searchEmployees($branch_id, $name_phone_email, $status, $type_employees) {
        $result = DB::table('branch_staff as bs')
                    ->select('us.id', 'us.name', 'bs.branch_id', 'us.phone', 'us.email', 'us.status',
                             'us.avatar_path', 'us.type', 'us.created_at', 'br.name as branch_name', 'ct.name as category_name', 'ct.id as categoryid', 'us.date_of_birth as birthday')
                    ->join('users as us', 'us.id', '=', 'bs.staff_id')
                    ->join('branch as br', 'br.id', '=', 'bs.branch_id')
                    ->join('category as ct', 'us.staff_type_id', '=', 'ct.id')
                    ->where('us.status', '=', EStatus::ACTIVE);
        if ($name_phone_email != '' && $name_phone_email != null) {
            $result->where(function($where) use ($name_phone_email) { 
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($name_phone_email, 'UTF-8')) . '%'])
                    ->orWhereRaw('lower(us.email) like ? ', ['%' . trim(mb_strtolower($name_phone_email, 'UTF-8')) . '%'])
                    ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($name_phone_email, 'UTF-8')) . '%']);
            });
        } 
        if ($branch_id != '' && $branch_id != null) {
            $result->where('bs.branch_id', '=', $branch_id);
        }
        if ($status != '' && $status != null) {
            $result->where('bs.status', '=', $status);
        } else {
            $result->where('bs.status', '!=', EStatus::DELETED);
            $result->where('br.status', '!=', EStatus::DELETED);
            $result->where('ct.status', '!=', EStatus::DELETED);
        }
        if ($type_employees != '' && $type_employees != null) {
            $result->where('us.staff_type_id', '=', $type_employees);
        }
        $results = $result->orderBy('id', 'desc')->paginate(15);
        return $results;
    }

    public function getStaffType() {
        $result = DB::table('category')->select('id', 'name', 'value')->where(['type' => EUser::TYPE_STAFF_SYNC, 'status' => EStatus::ACTIVE])->get();
        return $result;
    }

    public function deleteEmployeesUser($users_id) {
        $result = Users::where('id', '=', $users_id)->update(['status' => EStatus::DELETED]);
        return $result;
    }
}