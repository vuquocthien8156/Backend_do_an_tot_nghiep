<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\ConversationRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;

class ConversationService {
    protected $conversationRepository;

    public function __construct(ConversationRepository $conversationRepository) {
		$this->conversationRepository = $conversationRepository;
    }

    public function saveConversation($name_conversation, $name_admin, $member_id, $admin_id) {
        DB::beginTransaction();
        try {
            $saveConversation = $this->conversationRepository->saveConversation($name_conversation);
            $conversation_id = $saveConversation->id;
            $saveMember1 = $this->conversationRepository->saveConversationMember($conversation_id, $member_id, $name_admin);
            $saveMember2 = $this->conversationRepository->saveConversationMember($conversation_id, $admin_id, $name_conversation);
            if (isset($saveConversation) && isset($saveMember1) && isset($saveMember2)) {
                DB::commit();
                return $saveConversation;
            } else {
                DB::rollBack();
            }
        } catch (Exception $e) {
            logger('Fail Save conversation, name: ' . $name_conversation , ['e' => $e]);
            DB::rollBack();
        }
    }

    public function getInforAllUser() {
        return $this->conversationRepository->getInforAllUser();
    }

    public function getInfoUserByConversationId($conversation_id, $id_admin) {
        return $this->conversationRepository->getInfoUserByConversationId($conversation_id, $id_admin);
    }

    public function getInfoUserById($id_user) {
        return $this->conversationRepository->getInfoUserById($id_user);
    }

    public function checkUserHasInConversation($user_id) {
        return $this->conversationRepository-> checkUserHasInConversation($user_id);
    }

    public function checkAdminHasInConversation($conversation_id, $admin_id) {
        return $this->conversationRepository->checkAdminHasInConversation($conversation_id, $admin_id);
    }

    public function searchInfoUser($value) {
        return $this->conversationRepository->searchInfoUser($value);
    }
}