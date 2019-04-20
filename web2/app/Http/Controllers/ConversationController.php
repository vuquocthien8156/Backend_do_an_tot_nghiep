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
use App\Enums\EManufacture;
use App\Enums\ECodePermissionGroup;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Services\ConversationService;
use App\Services\AuthorizationService;
use App\Services\ConfigService;

class ConversationController extends Controller {

	public function __construct(ConversationService $conversationService, AuthorizationService $authorizationService, ConfigService $configService) {
        $this->conversationService = $conversationService;
        $this->authorizationService = $authorizationService;
        $this->configService = $configService;
    }

    public function twoDigitNumber($number) {
		return $number < 10 ? '0'.$number : $number;
    }

    // public function viewChat() {
    //     if (Gate::denies('enable_feature', ECodePermissionGroup::CHAT)) {
    //         return abort(403, 'Unauthorized action!');
    //     }
    //     $pathToResource = config('app.resource_url_path');
    //     return view('chat-user.chat-user', ['path_resource' => $pathToResource]);
    // }

    public function viewChat() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CHAT)) {
            return abort(403, 'Unauthorized action!');
        }
        $infoCustomer = $this->configService->infoCustomer();
        $pathToResource = config('app.resource_url_path');
        return view('chat-user.chat-user', ['path_resource' => $pathToResource, 'infoCustomer' => $infoCustomer]);
    }

    public function viewChat2() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CHAT)) {
            return abort(403, 'Unauthorized action!');
        }
        $infoCustomer = $this->configService->infoCustomer();
        $pathToResource = config('app.resource_url_path');
        return view('chat-user.chat-repair', ['path_resource' => $pathToResource, 'infoCustomer' => $infoCustomer]);
    }

    public function saveConversation(Request $request) {
        $name_conversation = $request->get('name_conversation');
        $member_id = $request->get('member_id');
        $admin_id = auth()->id();
        $saveConversation = $this->conversationService->saveConversation($name_conversation, $member_id, $admin_id);
        if (isset($saveConversation)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function getInforAllUser() {
        $info_all_user = $this->conversationService->getInforAllUser();
        if (isset($saveConversation)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'info_all_user' => $info_all_user]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function getInfoUser(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CHAT)) {
            return abort(403, 'Unauthorized action!');
        }
        $arr_conversation_id = $request->get('list_conversation_id');
        // $arrayId = explode(',', $arr_conversation_id);
        $arr_info_user = array();
        if ($arr_conversation_id == null || count($arr_conversation_id) < 1) {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
        foreach ($arr_conversation_id as $conversation_id) {
            $info_user = $this->conversationService->getInfoUserByConversationId(preg_replace('/\D/', '', $conversation_id), auth()->id());
            array_push($arr_info_user, $info_user[0]);
        }
        $pathToResource = config('app.resource_url_path');
        return \Response::json(['error' => ErrorCode::NO_ERROR, 'info_user' => $arr_info_user, 'path_resource' => $pathToResource]);
    }

    public function savePathAvatar(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CHAT)) {
            return abort(403, 'Unauthorized action!');
        }
        if ($request->file('file_image') != null || $request->file('file_image') != '') {
            $now = Carbon::now();
            $conversation_id = $request->get('conversation_id');
            $yy = date("y");
            $subName = "conversation/{$conversation_id}/{$yy}{$this->twoDigitNumber($now->day)}";         
            $destinationPath = config('app.resource_physical_path');
            $pathToResource = config('app.resource_url_path');
            $filename = str_random(30) . '.' . $request->file('file_image')->getClientOriginalExtension();
            $check = $request->file('file_image')->move($destinationPath . '/' . $subName, $filename);
            if (!file_exists($check)) {
                return \Response::json(false);
            }
            return $subName . '/' . $filename;
        } else {
            return \Response::json(false);
        }
    }

    public function checkUserHasConversation(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CHAT)) {
            return abort(403, 'Unauthorized action!');
        }
        $user_id = $request->get('id_user');
        $checkUserConversation = $this->conversationService->checkUserHasInConversation($user_id);
        if (isset($checkUserConversation[0]->id)) {
            foreach ($checkUserConversation as $key => $value) {
                $checkAdminConversation = $this->conversationService->checkAdminHasInConversation($value->conversation_id, auth()->id());
                if (isset($checkAdminConversation[0]->id)) {
                    return \Response::json(['has_conversation' => true, 'info_conversation' => $checkAdminConversation]);
                }
            }
            return \Response::json(['has_conversation' => false]);
        } else {
            return \Response::json(['has_conversation' => false]);
        }
    }

    public function createConversation(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CHAT)) {
            return abort(403, 'Unauthorized action!');
        }
        $user_id = $request->get('user_id');
        $name = $request->get('name');
        $createConversation = $this->conversationService->saveConversation((string)$name, auth()->user()->name, $user_id, auth()->id());
        if (isset($createConversation)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'id_conversation' => $createConversation->id]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function searchAutoInfoUser(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CHAT)) {
            return abort(403, 'Unauthorized action!');
        }
        $value = $request->get('term');
        $searchInfoUser = $this->conversationService->searchInfoUser($value['term']);
        if (isset($searchInfoUser)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'searchInfoUser' => $searchInfoUser]);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }
}