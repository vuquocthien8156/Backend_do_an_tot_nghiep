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
use App\Enums\EAppointmentType;
use App\Enums\ECodePermissionGroup;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Services\CustomerService;
use App\Traits\CommonTrait;
use Excel;
use App\Models\Users;
use App\Exports\CustomerExport;
use App\Exports\MemberCardExport;
use App\Exports\AppointmentExport;
use App\Enums\EManufacture;
use App\Services\ConfigService;
use App\Services\NotificationService;
use App\Services\AuthorizationService;
use App\Services\SyncDB411Service;
use App\Services\SyncAutoDB411Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class CustomerController extends Controller {
	use CommonTrait;

	private $customerService;

    public function __construct(SyncDB411Service $syncDB411Service, CustomerService $customerService, SyncAutoDB411Service $syncAutoDB411Service,
        ConfigService $configService, AuthorizationService $authorizationService, NotificationService $notificationService) {
        $this->customerService = $customerService;
        $this->configService = $configService;
        $this->authorizationService = $authorizationService;
        $this->syncDB411Service = $syncDB411Service;
        $this->notificationService = $notificationService;
        $this->syncAutoDB411Service = $syncAutoDB411Service;
    }

    public function twoDigitNumber($number) {
		return $number < 10 ? '0'.$number : $number;
    }

    public function checkAuthoziration() {
        if (Session::has('authorization_user')) {
            if (Gate::allows('enable_feature', ECodePermissionGroup::CUSTOMER)) {
                return redirect()->route('manage-customer');
            } elseif (Gate::allows('enable_feature', ECodePermissionGroup::SERVICE)) {
                return redirect()->route('config-view-rescue');
            } elseif (Gate::allows('enable_feature', ECodePermissionGroup::STAFF)) {
                return redirect()->route('employees-view');
            } elseif (Gate::allows('enable_feature', ECodePermissionGroup::NOTIFICATION)) {
                return redirect()->route('notification-view');
            } elseif (Gate::allows('enable_feature', ECodePermissionGroup::CONFIG)) {
                return redirect()->route('config-view-manufacture-model');
            } elseif (Gate::allows('enable_feature', ECodePermissionGroup::CHAT)) {
                return redirect()->route('chat-user-view');
            }
            return abort(403, 'Unauthorized action!');
        } else {
            return abort(403, 'Unauthorized action!');
        }
    }
    // Customer
    public function viewManageCustomer() { 
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $listBranch = $this->configService->getListBranch();
        $liststaff = $this->customerService->getStaffUsers();
        $listPartnerField = $this->customerService->getPartnerField();
        return view('customer.manage-customer', ['listBranch' => $listBranch,'listPartnerField' => $listPartnerField, 'liststaff' => $liststaff]);
    }

    public function doSearchCustomer(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        if ($request->ajax()) {
            $username_phone = $request->get('username_phone');
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');
            if (isset($from_date)) {
                $from_date = Carbon::parse($from_date)->toDateTimeString();
            } else {
                $from_date = null;
            }
            if (isset($to_date)) {
                $to_date = date("Y-m-d H:i:s", (strtotime(Carbon::parse($to_date)->toDateTimeString()) + (24 * 60 * 60 - 1)));
            } else {
                $to_date = null;
            }
            $status = $request->get('status'); 
            $partner_id = $request->get('partner_field');
            $infoExportExcel = ['username' => $username_phone, 'from_date' => $from_date, 'to_date' => $to_date, 'status' => $status, 'partner_field' => $partner_id];

            $page = 1;
            if ($request->get('page') !== null) {
                $page = $request->get('page');
            }
			$listSearchCustomer = $this->customerService->doSearchCustomer($username_phone, $status, $from_date, $to_date, $partner_id, $page);
            $timezone = $this->getUserTimezone();
			$tmp = $listSearchCustomer->map(function ($item) use ($timezone) {
                if ($item->avatar_path != null && $item->avatar_path != "" && strpos($item->avatar_path, 'https://') === false && strpos($item->avatar_path, 'http://') === false) {
                        $item->avatar_path = config('app.resource_url_path') . "/$item->avatar_path";
                }
                $name_partner = $this->customerService->getPartnerName($item->partner_id);
				return [
                    'id' => $item->id,
                    'phone' => $item->phone,
                    'name' => $item->name,
                    'address' => $item->address,
                    'avatar_path' => $item->avatar_path,
                    'email' => $item->email,
					'status' => EUser::valueToName($item->status),
                    'created_at' => isset($item->created_at) ? Carbon::parse($item->created_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT) : null,
                    'date_of_birth' => isset($item->date_of_birth) ? Carbon::parse($item->date_of_birth)->format(EDateFormat::MODEL_DATE_FORMAT_NORMAL) : null,
                    'partnerName' => isset($name_partner[0]->name) ? $name_partner[0]->name : null,
                    'partnerID' => isset($name_partner[0]->name) ? $name_partner[0]->id : null,
				];
            });
            //$tmp->appends($options);
            $listSearchCustomer->setCollection($tmp);

			return response()->json(['listSearch'=>$listSearchCustomer,'exportCustomerList'=>$infoExportExcel]);
		}
		return response()->json([]);
    }

    public function EditCustomer(Request $request) { 
       try {
            $id_user_edit = $request->get('id_user_edit');
            $name_edit = $request->get('name_employees_edit');
            $phone_edit = $request->get('phone_employees_edit');
            $address_edit = $request->get('address_edit');
            $birthday_edit = $request->get('birthday_edit');
            $partnerfield_edit = $request->get('partnerfield_edit');
            $partner_edit = $request->get('partner_edit');
            $avatar_path = $request->get('avatar_path_edit');
            $updated_at = Carbon::now();
            $updated_by = auth()->id();
            
            if ($request->file('files_edit') != null || $request->file('files_edit') != '') {
                
                $subName = 'user/profile/avatar'.$updated_at->year.$this->twoDigitNumber($updated_at->month).$this->twoDigitNumber($updated_at->day);
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename =  $subName . '/' . $request->file('files_edit')->getClientOriginalName();
                $check = $request->file('files_edit')->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
            }

            if ($partner_edit != '' && $partner_edit != null) {
                $editCustomer = $this->customerService->EditCustomerUser($id_user_edit, $name_edit, $phone_edit, $birthday_edit, $partner_edit, $address_edit, $updated_by, $updated_at, $avatar_path);
            } else {
                $editCustomer = $this->customerService->EditCustomerUser($id_user_edit, $name_edit, $phone_edit, $birthday_edit, $partnerfield_edit, $address_edit, $updated_by, $updated_at, $avatar_path);
            }
            
            $editAddress = $this->customerService->editAddressUser($id_user_edit, $address_edit);
            
            if (isset($editCustomer) && isset($editAddress)) {
                return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
            } else {
                return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
            }
        } catch (\Exception $e) {
            logger('Fail to update feedback' . $name_edit , ['e' => $e]);
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function doExportExcelCustomer(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $username_phone = $request->get('username');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $status = $request->get('status');
        $partner_id = $request->get('partner_field');
        return Excel::download(new CustomerExport($username_phone, $from_date, $to_date, $status, $partner_id), 'customer-411.xlsx');
    }

    public function deleteCustomer(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $isDelete = $request->get('id_customer');
        $deleteCustomer = $this->customerService->deleteCustomer($isDelete);
        if (isset($deleteCustomer)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function upgradeCustomer(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        try {
            $name = $request->get('name_employees');
            $phone = $request->get('phone_employees');
            $email = $request->get('email_employees');
            $branch_id = $request->get('branch');
            $type_employees = $request->get('type_employees');
            $avatar_path = $request->get('avatar_path');
            $id_user = $request->get('id_user');
            $created_by = auth()->id();
            $now = Carbon::now();
            if ($request->file('files') != null || $request->file('files') != '') {
                $subName = 'employees/avatar/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);         
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename =  $subName . '/' . $request->file('files')->getClientOriginalName();
                $check = $request->file('files')->move($destinationPath.'/'.$subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $filename;
            }
            $saveEmployees = $this->customerService->saveUpgradeUser($id_user, $name, $phone, $email, $avatar_path, $created_by, $type_employees);
            $saveStaffBranch = $this->customerService->saveStaffBranch($branch_id, $id_user, $created_by);
            if (isset($saveStaffBranch) && isset($saveStaffBranch)) {
                return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
            } else {
                return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
            }
        } catch (\Exception $e) {
            logger('Fail Save branch staff, branch_id: ' . $branch_id , ['e' => $e]);
            return null;
        }
    }

    // Card Member
    public function viewManageCardMember() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $listManufacture = $this->customerService->getManufacture();
        return view('customer.manage-card-member', ['listManufacture' => $listManufacture]);
    }

    public function doSearchCardMember(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        if ($request->ajax()) {
            $username_phone_number_vehicle = $request->get('username_phone_number_vehicle');
            $manufacture = $request->get('manufacture');
            $model = $request->get('model');
            $code = $request->get('code');
            $status1 = $request->get('status');
            if ($status1 != "") {
                if ($status1 == EStatus::DELETED) {
                    $status = $status1; $approved = null; $vehicle_card_status = null;
                } else {
                    list($status, $approved, $vehicle_card_status) = explode(",", $status1);
                }
            } else {
                $status = null; $approved = null; $vehicle_card_status = null;
            }
            $infoExportExcel = ['username_phone_number_vehicle'=>$username_phone_number_vehicle, 'manufacture'=>$manufacture, 'model'=>$model, 'code'=>$code, 'status'=>$status, 'approved'=>$approved, 'vehicle_card_status'=>$vehicle_card_status];
            
            $page = 1;
            if ($request->get('page') !== null) {
                $page = $request->get('page');
            }
			$listCardMember = $this->customerService->doSearchCardMember($username_phone_number_vehicle, $manufacture, $model, $code, $status, $approved, $vehicle_card_status, $page);
            
            $timezone = $this->getUserTimezone();
			$tmp = $listCardMember->map(function ($item) use ($timezone) {
                $getNameManufactureById = $this->customerService->getNameManufactureById($item->vehicle_manufacture_id);
                $getNameManufactureModalById = $this->customerService->getNameManufactureById($item->vehicle_model_id);
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'phone' => $item->phone,
                    'name_card' => $item->name_card,
                    'name_user' => $item->name_user,
                    'code' => $item->code,
                    'vehicle_manufacture_id' => isset($getNameManufactureById[0]->name) ? $getNameManufactureById[0]->name : null,
                    'vehicle_model_id' => isset($getNameManufactureModalById[0]->name) ? $getNameManufactureModalById[0]->name : null,
                    'status' => $item->status,
                    'vehicle_number' => $item->vehicle_number,
                    'vehicle_color' => $item->vehicle_color,
                    'created_at' =>isset($item->created_at) ? Carbon::parse($item->created_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_NORMAL) : null,
                    'approved_at' =>isset($item->approved_at) ? Carbon::parse($item->approved_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_NORMAL) : null,
                    'expired_at' =>isset($item->expired_at) ? Carbon::parse($item->expired_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_NORMAL) : null,
                    'bank_transfer_info' => $item->bank_transfer_info,
                    'approved' => $item->approved,
                    'vehicle_card_status' => $item->vehicle_card_status,
                ];
            });

			$listCardMember->setCollection($tmp);
			return response()->json(['listCard'=>$listCardMember,'listCardExport'=>$infoExportExcel]);
		}
		return response()->json([]);
    }

    public function getManufactureModel(Request $request) {
        if ($request->ajax()) {
            $id_manufacture = $request->get('id_manufacture');
            $listManufacture = $this->customerService->getManufactureModal($id_manufacture);
			return response()->json($listManufacture);
		}
		return response()->json([]);
    }

    public function doExportExcelCardMember(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $username_phone_number_vehicle = $request->get('username_phone');
        $manufacture = $request->get('manufacture');
        $model = $request->get('model');
        $code = $request->get('code');
        $status = $request->get('status');
        $approved = $request->get('approved');
        $vehicle_card_status = $request->get('vehicle_card_status');
        return Excel::download(new MemberCardExport($username_phone_number_vehicle, $manufacture, $model, $code, $status, $approved, $vehicle_card_status), 'member-card-411.xlsx');
    }

    public function deleteCardMember(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_card_member = $request->get('id_card_member');
        $deleted_by = auth()->id();
        $deleteCardMember = $this->customerService->deleteCardMember($id_card_member, $deleted_by);
        if (isset($deleteCardMember)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function saveCodeCardMember(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_user = $request->get('id_user');
        $id_card_member = $request->get('id_card_member');
        $code = $request->get('code');
        $name_card = $request->get('name_card');
        $approved_by = auth()->id();
        $approved_at = Carbon::now();
        $saveCodeCardMember = $this->customerService->saveCodeCardMember($id_card_member, $code, $name_card, $approved_by, $approved_at);
        if (isset($saveCodeCardMember)) {
            $title = 'Hệ Thống Sửa Xe 411';
            $content = 'Thẻ thành viên của bạn đã được kích hoạt!';
            $type_notification = 101;
            $sendNotification = $this->notificationService->saveNotification($title, $content, $type_notification, $id_user);
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function updateNameCodeCardMember(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_card_member = $request->get('id_card_member');
        $code_card_member = $request->get('code_card_member');
        $name_card_member = $request->get('name_card_member');
        $updateCardMember = $this->customerService->updateNameCodeCardMember($id_card_member, $name_card_member, $code_card_member);
        if (isset($updateCardMember)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function viewManageAppointment() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $listBranch = $this->configService->getListBranch();
        $infoCustomer = $this->configService->infoCustomer();
        return view('customer.manage-schedule-appointment', ['listBranch' => $listBranch, 'infoCustomer' => $infoCustomer]);
    }

    public function doSearchAppointment(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        if ($request->ajax()) {
            $username_phone_number = $request->get('username_phone_number');
            $type_appointment = $request->get('type_appointment');
            $branch = $request->get('branch');
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');
            if (isset($from_date)) {
                $from_date = Carbon::parse($from_date)->toDateTimeString();
            } else {
                $from_date = null;
            }
            if (isset($to_date)) {
                $to_date = date("Y-m-d H:i:s", (strtotime(Carbon::parse($to_date)->toDateTimeString()) + (24 * 60 * 60 - 1)));
            } else {
                $to_date = null;
            }

            $infoExportExcel = ['username_phone_number'=>$username_phone_number, 'type_appointment'=>$type_appointment, 'branch'=>$branch, 'from_date'=>$from_date,'to_date'=>$to_date];

            $page = 1;
            if ($request->get('page') !== null) {
                $page = $request->get('page');
            }
			$listAppointment = $this->customerService->doSearchAppointment($username_phone_number, $type_appointment, $branch, $from_date, $to_date, $page);
            $timezone = $this->getUserTimezone();
			$tmp = $listAppointment->map(function ($item) use ($timezone) {
                return [
                    'id' => $item->id,
                    'phone' => $item->phone,
                    'name_user' => $item->name_user,
                    'name_branch' => $item->name_branch,
                    'branch_id' => $item->branch_id,
                    'appointment_at' => isset($item->appointment_at) ? Carbon::parse($item->appointment_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT) : null,
                    'type' => EAppointmentType::valueToName($item->type),
                    'type_value' => $item->type,
                    'note' => $item->note,
                    'enable_reminder' => $item->enable_reminder,
                ];
            });

			$listAppointment->setCollection($tmp);
			return response()->json(['listAppointment'=>$listAppointment,'listAppointmentExport'=>$infoExportExcel]);
		}
		return response()->json([]);
    }

    //Appointment
    public function viewAppointment() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $listBranch = $this->configService->getListBranch();
        $infoCustomer = $this->configService->infoCustomer();
        return view('customer.make-appointment', ['listBranch' => $listBranch, 'infoCustomer' => $infoCustomer]);
    }

    public function saveAppointment(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $typeAppointment = $request->get('type_appointment');
        $reminder = $request->get('reminder');
        $time_config = $request->get('time_config');
        $note = $request->get('note');
        $branch = $request->get('branch');
        $id_appointment = $request->get('id_appointment');
        $numberphone_user = $request->get('numberphone_user');
        $appointment_at = Carbon::parse($time_config);
        $customer = $this->customerService->getIdCustomerByPhone($numberphone_user);
        
        if (isset($customer[0]->id)) {
            $user_id = $customer[0]->id;
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'No Found Customer!']);
        }
        $created_by = auth()->id();
        $saveAppointment = $this->customerService->saveOrUpdateAppointment($user_id, $branch, $typeAppointment, $appointment_at, $note, $reminder, $created_by, $id_appointment);
        if (isset($saveAppointment)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function doExportExcelAppointment(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $username_phone_number = $request->get('username_phone_number');
        $type_appointment = $request->get('type_appointment');
        $branch = $request->get('branch');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        return Excel::download(new AppointmentExport($username_phone_number, $type_appointment, $branch,  $from_date, $to_date), 'appointment-411.xlsx');
    }

    public function deleteAppointment(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_appointment = $request->get('id_appointment');
        $deleteAppointment = $this->customerService->deleteAppointmentSchedule($id_appointment);
        if (isset($deleteAppointment)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    // public function syncMembershipCard(Request $request) {
    //     if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
    //         return abort(403, 'Unauthorized action!');
    //     }
    //     $syncUser = Artisan::call('sync:411', ['--sync-user' => true]);
    //     $syncCardMember = Artisan::call('sync:411', ['--sync-membership-card' => true]);
    //     return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']); 
    // }

    public function getPartner(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CUSTOMER)) {
            return abort(403, 'Unauthorized action!');
        }
        
        if($request->ajax()) {
            if ($request->get('partner')) {
                $getPartner = $this->customerService->getPartnerParentId($request->get('partner'));

                if (isset($getPartner)) {
                    return response()->json($getPartner);
                } else {
                    return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
                }
            } else {
                $partner_id = $request->get('partnerField');
                $getPartner = $this->customerService->getPartner($partner_id);
                
                if (isset($getPartner)) {
                    return response()->json($getPartner);
                } else {
                    return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
                }
            }
        }
        return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
    }
}
