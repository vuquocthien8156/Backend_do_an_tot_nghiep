<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Models\Users;
use App\Models\Conversation;
use App\Models\ConversationMember;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConversationRepository {

	public function __construct(Users $users, Conversation $conversation, ConversationMember $conversationMember) {
        $this->users = $users;
        $this->conversation = $conversation;
        $this->conversationMember = $conversationMember;
    }

    public function saveConversation($name_conversation) {
        try {
            $now = Carbon::now();
            $conversation = new Conversation();
            $conversation->name = $name_conversation;
            $conversation->status = EStatus::ACTIVE;
            $conversation->created_at = $now;
            $conversation->save();
            return $conversation;
        } catch (\Exception $e) {
            logger('Fail Save conversation, name: ' . $name_conversation . ' time:' . $now, ['e' => $e]);
            return null;
        }
    }

    public function saveConversationMember($conversation_id, $user_id, $name_conversation) {
        try {
            $now = Carbon::now();
            $conversationMember = new ConversationMember();
            $conversationMember->conversation_id = $conversation_id;
            $conversationMember->user_id = $user_id;
            $conversationMember->conversation_name = $name_conversation;
            $conversationMember->save();
            return $conversationMember;
        } catch (\Exception $e) {
            logger('Fail Save Conversation Member, id_conversation: ' . $conversation_id . ' time:' . $now, ['e' => $e]);
            return null;
        }
    }

    public function getInforAllUser() {
        try {
            $result = DB::table('users')->select('id', 'name', 'avatar_path')
                        ->where('status', '=', EStatus::ACTIVE)->get();
            return $result;
        } catch (\Exception $e) {
            logger("Failed to get Info ALl User. message: " . $e->getMessage());
            return null;
        }
    }

    public function getInfoUserByConversationId($conversation_id, $id_admin) {
        try {
            $result = DB::table('conversation_member as cm')->select('cm.conversation_id', 'us.name', 'us.avatar_path', 'us.id as user_id')
                        ->join('users as us', 'us.id', '=', 'cm.user_id')
                        ->where([['cm.conversation_id', '=', $conversation_id], ['cm.user_id', '!=', $id_admin]])
                        ->get();
            return $result;
        } catch (\Exception $e) {
            logger('Failed to get Info User By conversation_id message: ' . $e->getMessage());
            return null;
        }
    }

    public function getInfoUserById($id_user) {
        try {
            $result = DB::table('users')->select('id', 'name', 'avatar_path')
                        ->where([['id', '=', $id_user], ['status', '=', EStatus::ACTIVE]])->get();
            return $result;
        } catch (\Exception $e) {
            logger("Failed to get Info User. message: " . $e->getMessage());
            return null;
        }
    }

    public function checkUserHasInConversation($user_id) {
        try {
            $result = DB::table('conversation_member')->select('id', 'conversation_id', 'user_id')
                        ->where([['user_id', '=', $user_id], ['deleted_conversation', '=', false]])->get();
            return $result;
        } catch (\Exception $e) {
            logger("Failed to get checkUserHasInConversation user_id: ${$user_id}. message: " . $e->getMessage());
            return null;
        }
    }

    public function checkAdminHasInConversation($conversation_id, $admin_id) {
        try {
            $result = DB::table('conversation_member')->select('id', 'conversation_id', 'user_id')
                        ->where([['user_id', '=', $admin_id], ['conversation_id', '=', $conversation_id]])->get();
            return $result;
        } catch (\Exception $e) {
            logger("Failed to get checkAdminHasInConversation conversation_id: ${$conversation_id}. message: " . $e->getMessage());
            return null;
        }
    }

    public function searchInfoUser($value) {
        $result = DB::table('users as us')->select('us.id', 'us.phone', 'us.name')
                    ->where([['us.status', '=', EStatus::ACTIVE], ['us.type', '<>', EUser::TYPE_USER_WEB]]);
        if ($value != '' && $value != null) {
            $result->where(function($where) use ($value) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($value, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($value, 'UTF-8')) . '%']);
            });
        }
        $result = $result->orderBy('id', 'desc')->limit(20)->get(); 
        return $result;
    }
}