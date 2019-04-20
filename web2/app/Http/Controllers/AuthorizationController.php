<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EDateFormat;
use App\Enums\ELanguage;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Enums\EUserRole;
use App\Enums\ECodePermissionGroup;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Traits\CommonTrait;
use Excel;
use App\Enums\EManufacture;
use App\Services\AuthorizationService;

class AuthorizationController extends Controller {
	use CommonTrait;

	public function __construct(AuthorizationService $authorizationService) {
        $this->authorizationService = $authorizationService;
    }

    public function viewAuthorization() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $getPermissionGroup = $this->authorizationService->getPermisstionGroup();
        return view('config.users-role', ['permissionGroup' => $getPermissionGroup]);
    }

    public function getListAuthorizationUserWeb(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        if ($request->ajax()) {
            $getListAuthorizationUserWeb = $this->authorizationService->getListAuthorizationUserWeb();
			$tmp = $getListAuthorizationUserWeb->map(function ($item) {
                $getListAccess = $this->authorizationService->getListAccess($item->id);
                
                if(count($getListAccess) > 0) {
                    $arr_permission_group_id = [];
                    foreach ($getListAccess as $key => $value) {
                        array_push($arr_permission_group_id, $value->permission_group_id);
                    }
                }
                
				return [
                    'id' => $item->id,
                    'phone' => $item->phone,
                    'name' => $item->name,
					'status' => EUser::valueToName($item->status),
                    'email' => $item->email,
                    'listAccess_id' => $arr_permission_group_id,
                    'listAccess' => $getListAccess
				];
            });
            $getListAuthorizationUserWeb->setCollection($tmp);

			return response()->json($getListAuthorizationUserWeb);
		}
		return response()->json([]);
    }

    public function saveAuthorizationUserWeb(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $name = $request->get('name');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $password = $request->get('password');
        $permission_group = $request->get('permission_group');
        $created_by = auth()->id();
        $saveAuthorizationUserWeb = $this->authorizationService->saveAuthorizationUserWeb($name, $phone, $email, $password, $permission_group, $created_by);
        if (isset($saveAuthorizationUserWeb)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }
    
    public function deleteAuthorizationUserWeb(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $user_id = $request->get('user_id');
        $deleteAuthorizationUserWeb = $this->authorizationService->deleteAuthorizationUserWeb($user_id);
        if (isset($deleteAuthorizationUserWeb)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function updateAuthorizationUserWeb(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $user_id = $request->get('user_id');
        $name = $request->get('name');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $permission_group = $request->get('permission_group');
        $updated_by = auth()->id();
        $updateAuthorizationUserWeb = $this->authorizationService->updateAuthorizationUserWeb($user_id, $name, $phone, $email, $permission_group, $updated_by);
        if (isset($updateAuthorizationUserWeb)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }
}