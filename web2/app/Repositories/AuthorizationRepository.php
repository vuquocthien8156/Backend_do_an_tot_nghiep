<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Models\Users;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthorizationRepository {

	public function __construct(Users $users, UserRolePermission $userRolePermission) {
        $this->users = $users;
        $this->userRolePermission = $userRolePermission;
    }

    public function getAuthorizationUser($id_user) {
        $result = DB::table('user_role_permission as urp')
                    ->select('urp.permission_group_id', 'ao.code')
                    ->join('acl_object as ao', 'ao.id', '=', 'urp.permission_group_id')
                    ->where('urp.user_id', $id_user)->get();
        return $result;
    }

    public function getPermisstionGroup() {
        $result = DB::table('acl_object')->select('id', 'name', 'code')
                    ->where('source', ECodePermissionGroup::SOURCE_SYSTERM)
                    ->Orderby('id')
                    ->get();
        return $result;
    }

    public function saveAuthorizationUserWeb($name, $phone, $email, $password, $permission_group, $created_by) {
        return DB::transaction(function () use ($name, $phone, $email, $password, $permission_group, $created_by) {
            $now = Carbon::now();
            $user = new Users();
			$user->name = $name;
            $user->phone = $phone;
            $user->email = mb_strtolower(trim($email));
            $user->password = Hash::make($password);
            $user->status = EStatus::ACTIVE;
            $user->type = EUser::TYPE_USER_WEB;
            $user->created_at = $now;
            $user->created_by = $created_by;
            $user->save();

            foreach ($permission_group as $key => $value) {
                $permission = new UserRolePermission();
                $permission->user_id = $user->id;
                $permission->permission_group_id = $value;
                $permission->save();
            }
            if (isset($permission) && isset($user)) {
                return true;
            }
			return null;
		});
    }

    public function deleteRolePermissionByUserId($user_id) {
        $result = DB::table('user_role_permission')->where('user_id', '=', $user_id)->delete();
        return $result;
    }

    public function saveUserRolePermission($user_id, $value) {
        try {
            $permission = new UserRolePermission();
            $permission->user_id = $user_id;
            $permission->permission_group_id = $value;
            $permission->save();
            return $permission;
        } catch (\Exception $e) {
            logger('Fail Save user role permission user_id: ' . $user_id, ['e' => $e]);
            return null;
        }
    }

    public function getAuthorizationUserWeb() {
        $result = DB::table('users')->select('id', 'name', 'email', 'phone','status')
                ->where([['status', '=', EStatus::ACTIVE], ['type', '=', EUser::TYPE_USER_WEB]])
                ->paginate(15);
        return $result;
    }

    public function checkAuthorizationPage($user_id, $code_page) {
        try {
            $result = DB::table('user_role_permission as urp')->select('urp.id', 'ao.code')
                    ->join('acl_object as ao', 'ao.id', '=', 'urp.permission_group_id')
                    ->where('urp.user_id', $user_id)
                    ->get();
            foreach ($result as $value) {
                if($value->code === $code_page) {
                    return $result;
                }
            }
            return null;
        } catch (\Exception $e) {
            logger('Fail check authoziration page, code page: '. $code_page , ['e' => $e]);
            return null;
        }
       
    }

    public function getListAccess($id_user) {
        $result = DB::table('user_role_permission as urp')
                    ->select('urp.permission_group_id', 'ao.code', 'ao.name')
                    ->join('acl_object as ao', 'ao.id', '=', 'urp.permission_group_id')
                    ->where('urp.user_id', $id_user)->get();
        return $result;
    }

    public function deleteAuthorizationUserWeb($user_id) {
        $result = DB::table('users')->where('id', $user_id)->update(['status' => EStatus::DELETED]);
        return $result;
    }

    public function updateAuthorizationUserWeb($user_id, $name, $phone, $email, $updated_by) {
        $now = Carbon::now();
        $result = DB::table('users')->where('id', $user_id)
                                    ->update(['name' => $name, 'phone' => $phone, 
                                              'email' => $email, 'updated_by' => $updated_by, 
                                              'updated_at' => $now]);
        return $result;
    }

}