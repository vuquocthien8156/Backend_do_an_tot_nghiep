<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Enums\ERescueRequestStatus;
use App\Enums\ENotificationType;
use App\Models\RescueRequest;
use App\Models\Branch;
use App\Models\Users;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\EDateFormat;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use App\Services\FirebaseService;
use App\Models\UserDevice;
use App\Services\NotificationService;
use App\Enums\EUserDeviceType;

class RescueRequestRepository {

	public function __construct(Notification $notification, RescueRequest $rescueRequest, Users $users, NotificationService $notificationService) {
        $this->rescueRequest = $rescueRequest;
        $this->users = $users;
        $this->notification = $notification;
        $this->notificationService = $notificationService;
    }

    public function getRescueRequestById($id) {
        return $this->rescueRequest->find($id);
    }
    
    public function getInfoUser($id) {
        return $this->users->find($id);
    }

    public function sendNotificationForUser($content, $id_user, $title, $type_notification) {
        //Send Message for Devide Id
        $messaging = FirebaseService::messaging();
        $messagingNotification = \Kreait\Firebase\Messaging\Notification::fromArray([
            'title' => $title,
            'body' => $content,
        ]);
        $messagingData = [
            'type' => "$type_notification",
            'title' => $title,
            'body' => $content,
            'android_channel_id' => "abcdef",
        ];
        $config = AndroidConfig::fromArray([
            'priority' => 'high',
        ]);
        $userDevices = UserDevice::where([['user_id', '=', $id_user], ['status', '=', EStatus::ACTIVE]])->get();
        foreach ($userDevices as $device) {
            try {
                if ($device->os_type == EUserDeviceType::DEVICE_IOS) {
                    $message = CloudMessage::withTarget('token', $device->device_token)
                                            ->withNotification($messagingNotification)
                                            ->withData($messagingData);
                } else {
                    $message = CloudMessage::withTarget('token', $device->device_token)->withData($messagingData);
                }
                $messaging->send($message);
            } catch (\Exception $e) {
                logger('error when send GCM message', ['e' => $e]);
            }
        }
        return true;
    }

    public function saveNotification($title, $content, $type_notification, $id_user) {
        try {
            // Save noyification into DB
            $notification = new Notification();
            $now = Carbon::now();
            $notification->content = $content;
            $notification->title = $title;
            $notification->type = $type_notification;
            $notification->is_seen = false;
            $notification->created_at = $now;
            $notification->user_id = $id_user;
            $notification->save();
            $this->sendNotificationForUser($content, $id_user, $title, $type_notification);
            return $notification;
		} catch (\Exception $e) {
			logger("Failed to Save Notification message: " . $e->getMessage());
			return null;
		}
    }

    public function getUserRescue() {
        try {
            $resurlt = DB::table('rescue_request as rr')
                        ->select('rr.id', 'rr.user_id', 'rr.latitude', 'rr.longitude', 'rr.assigned_rescuer_at', 'rr.rescure_status', 'rr.estimated_distance', 'rr.service_price', 'rr.admin_note', 'rr.created_at', 'rr.status','us.name', 'us.phone', 'us_staff.name as name_staff', 'us_staff.phone as phone_staff')
                        ->join('users as us', 'rr.user_id', '=', 'us.id')
                        ->leftJoin('users as us_staff', 'rr.assigned_staff_id', '=', 'us_staff.id')
                        ->where('us.status', '=', EStatus::ACTIVE)->orderBy('rr.created_at', 'desc')->get();
            return $resurlt;
        } catch (\Exception $e) {
            logger("Failed to Get rescue request user message: " . $e->getMessage());
            return null;
        }
    }

    public function getListBranchStaff($branch_id_resuce) {
        try {
            $resurlt = DB::table('branch_staff as bs')
                        ->select('bs.staff_id', 'bs.branch_id', 'us.name', 'us.avatar_path', 'us.phone', 'bs.id')
                        ->join('users as us', 'bs.staff_id', '=', 'us.id')
                        ->where([['bs.branch_id', '=', $branch_id_resuce], ['us.status', '=', EStatus::ACTIVE], ['bs.status', '=', EStatus::ACTIVE], ['us.type', '=', EUser::TYPE_STAFF]])
                        ->get();
            return $resurlt;
        } catch (\Exception $e) {
            logger("Failed to Get rescue request user message: " . $e->getMessage());
            return null;
        }
    }

    public function saveAssignStaffRescue($branch_id_rescue, $id_staff_rescue, $distance, $price, $note, $id_rescue_request, $assigned_rescuer_by) {
        try {
                $now = Carbon::now();
                $result = DB::table('rescue_request')->where('id', $id_rescue_request)
                            ->update(['rescure_status' => ERescueRequestStatus::ASSIGNED_STAFF, 
                                      'assigned_shop_id' => $branch_id_rescue,
                                      'assigned_staff_id' => $id_staff_rescue,
                                      'assigned_rescuer_at' => $now,
                                      'assigned_rescuer_by' => $assigned_rescuer_by,
                                      'estimated_distance' => $distance,
                                      'service_price' => $price,
                                      'admin_note' => $note,
                                      'updated_at' => $now,
                                      'updated_by' => $assigned_rescuer_by
                                      ]);
                //Send notification
                $rescue_request = $this->getRescueRequestById($id_rescue_request);
                $id_user = $rescue_request->user_id;
                $name_staff = $this->getInfoUser($id_staff_rescue);
                $content = 'Nhân viên '  . $name_staff->name . ' đang tới cứu hộ cho bạn!';
                $title = 'Hệ Thống Sửa Xe 411';
                $type_notification = ENotificationType::RESCUE_REQUEST_ASSIGNED;

                $this->notificationService->saveNotification($title, $content, $type_notification, $id_user);

                return $result;
        } catch (\Exception $e) {
            logger('Error save assign staff rescue', ['e' => $e]);
            return null;
        }
    }

    public function completeRescue($id_rescue_request, $updated_by) {
        try {
            $now = Carbon::now();
            $result = DB::table('rescue_request')->where('id', $id_rescue_request)
                        ->update(['rescure_status' => ERescueRequestStatus::RESCUE_COMPLETE, 'rescue_completed_at' => $now]);
            return $result;
        } catch (\Exception $e) {
            logger('Error save completed rescue', ['e' => $e]);
            return null;
        }
    }

    public function deleteRescue($id_rescue_request, $deleted_by) {
        try {
            $now = Carbon::now();
            $result = DB::table('rescue_request')->where('id', $id_rescue_request)
                        ->update(['status' => EStatus::DELETED, 'deleted_by' => $deleted_by, 'deleted_at' => $now]);
            return $result;
        } catch (\Exception $e) {
            logger('Error save completed rescue', ['e' => $e]);
            return null;
        }
    }
}