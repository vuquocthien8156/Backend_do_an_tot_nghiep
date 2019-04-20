<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Models\Users;
use App\Models\Appointment;
use App\Models\BranchStaff;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\EPartnerType;

class CustomerRepository {

	public function __construct(Users $users, Appointment $appointment, BranchStaff $branchStaff) {
        $this->users = $users;
        $this->appointment = $appointment;
        $this->branchStaff = $branchStaff;
    }
    
    public function doSearchCustomer($username_phone, $status, $from_date, $to_date, $partner_id, $page = 1, $pageSize = 15) {
        $result = DB::table('users as us')->select('us.id', 'us.phone', 'us.name', 'us.avatar_path', 'us.email', 'us.created_at', 'us.phone', 'us.status', 'us.date_of_birth', 'us_ad.address', 'us.partner_id')
                                          ->leftJoin('user_address as us_ad', 'us_ad.user_id', '=', DB::raw('us.id and us_ad.is_default = true'))
                                          ->where([['us.type', '=', EUser::TYPE_USER]]);
        if ($username_phone != '' && $username_phone != null) {
            $result->where(function($where) use ($username_phone) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.email) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%']);
                });
        } 
        if ($from_date != '' && $from_date != null) {
            $result->where('us.created_at', '>', $from_date);
        }

        if ($to_date != '' && $to_date != null) {
            $result->where('us.created_at', '<', $to_date);
        }

        if ($status != '' && $status != null) {
            $result->where('us.status', '=', $status);
        } else {
            $result->where('us.status', '<>', EStatus::DELETED);
        }

        if ($partner_id != '' && $partner_id != null) {
            $result->where('us.partner_id', '=', $partner_id);
        }
        $result = $result->orderBy('id', 'desc')->paginate(15); 

		return $result;
    }

    public function EditCustomerUser($id_user_edit, $name_edit, $phone_edit, $birthday_edit, $partnerfield_edit, $address_edit, $updated_by, $updated_at, $avatar_path) {
        try {
            $result = DB::table('users')->where('id', '=', $id_user_edit)
                        ->update([  'name' => $name_edit,
                                    'phone' => $phone_edit,
                                    'date_of_birth' => $birthday_edit,
                                    'partner_id' => $partnerfield_edit,
                                    'address' => $address_edit, 
                                    'updated_by' => $updated_by,
                                    'updated_at' => $updated_at,
                                    'avatar_path' => $avatar_path,
                                ]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to update. message: " . $e->getMessage());
            return null;
        }
    }

    public function doExportExelCustomer($username_phone, $status, $from_date, $to_date) {
         $result = DB::table('users as us')->select('us.id', 'us.phone', 'us.name', 'us.created_at', 'us.phone', 'us.status', 'us.date_of_birth')
                    ->where('us.type', '=', EUser::TYPE_USER);
        if ($username_phone != '' && $username_phone != null) {
            $result->where(function($where) use ($username_phone) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.email) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%']);
                });
        } 
        if ($from_date != '' && $from_date != null) {
            $result->where('us.created_at', '>', $from_date);
        }

        if ($to_date != '' && $to_date != null) {
            $result->where('us.created_at', '<', $to_date);
        }

        if ($status != '' && $status != null) {
            $result->where('us.status', '=', $status);
        } else {
            $result->where('us.status', '<>', EStatus::DELETED);
        }
        $result = $result->orderBy('us.id', 'desc')->get(); 

        $timezone = $this->getUserTimezone();
        foreach ($result as $key => $item) {
            $item->created_at =  Carbon::parse($item->created_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT);
            $item->date_of_birth = Carbon::parse($item->date_of_birth)->format(EDateFormat::MODEL_DATE_FORMAT_NORMAL);
            $item->status = EUser::valueToName($item->status);
        }
		return $result;
    }

    public function deleteCustomer($id_customer) {
        try {
            $result = DB::table('users')->where('id', '=', $id_customer)->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to delete Customer. message: " . $e->getMessage());
            return null;
        }
    }

    public function saveUpgradeUser($id_user, $name, $phone, $email, $avatar_path, $created_by, $type_employees) {
        try {
            $now = Carbon::now();
            $result = DB::table('users')->where('id', '=', $id_user)
                                        ->update(['name' => $name,
                                                  'phone' => $phone,
                                                  'email' => $email,
                                                  'type' => EUser::TYPE_STAFF,
                                                  'avatar_path' => $avatar_path,
                                                  'updated_by' => $created_by,
                                                  'updated_at' => $now,
                                                  'changed_type_to_staff_at' => $now,
                                                  'changed_type_to_staff_by' => $created_by, 
                                                  'staff_type_id' => $type_employees]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to upgrade user to staff. message: " . $e->getMessage());
            return null;
        }
    }

    public function saveStaffBranch($branch_id, $id_user, $created_by) {
        try {
            $now = Carbon::now();
            $users = new BranchStaff();
            $users->status = EStatus::ACTIVE;
            $users->staff_id = $id_user;
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
    //Card member
    public function doSearchCardMember($username_phone, $manufacture, $model, $code, $status, $approved, $vehicle_card_status, $page = 1, $pageSize = 15) {
        $result = DB::table('membership_card as ms')
                    ->select('ms.id', 'ms.code', 'us.name as name_user', 'ms.name as name_card', 'ms.status', 'ms.vehicle_type', 'ms.vehicle_manufacture_id',
                             'ms.vehicle_model_id', 'ms.vehicle_number', 'ms.vehicle_color', 'ms.bank_transfer_info', 'ms.created_at', 
                             'ms.approved_at', 'ms.expired_at', 'ms.approved', 'ms.vehicle_card_status', 'us.name', 'us.phone', 'us.id as user_id')
                    ->join('users as us', 'us.id', '=', 'ms.user_id');
            
        if ($username_phone != '' && $username_phone != null) {
            $result->where(function($where) use ($username_phone) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(ms.vehicle_number) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%']);
            });
        }

        if ($manufacture != '' && $manufacture != null) {
            $result->where('ms.vehicle_manufacture_id', '=', $manufacture);
        }
        if ($model != '' && $model != null) {
            $result->where('ms.vehicle_model_id', '=', $model);
        }
        if ($code != '' && $code != null) {
            $result->where('ms.code', 'like', '%' . $code . '%');
        }
        if ($approved != '' && $approved != null && $approved != 'null') {
            $result->where('ms.approved', '=', $approved);
        }
        if ($vehicle_card_status != '' && $vehicle_card_status != null) {
            $result->where('ms.vehicle_card_status', '=', $vehicle_card_status);
        }
        if ($status != '' && $status != null) {
            $result->where('ms.status', '=', $status);
        } else {
            $result->where('ms.status', '!=', EStatus::DELETED);
            $result->where('ms.status', '!=', 2);
        }
        $result = $result->where([
            ['us.status', '=', EStatus::ACTIVE]
        ])->orderBy('id', 'desc')->paginate(15);
		return $result;
    }

    public function doExportExelCardMember($username_phone, $manufacture, $model, $code, $status) {
        $result = DB::table('membership_card as ms')
                    ->select('us.name', 'us.phone', 'ms.vehicle_number', 'ms.vehicle_manufacture_id', 'ms.vehicle_model_id', 'ms.vehicle_color', 'ms.bank_transfer_info', 'ms.code', 'ms.status')
                    ->join('users as us', 'us.id', '=', 'ms.user_id');
            
        if ($username_phone != '' && $username_phone != null) {
            $result->where(function($where) use ($username_phone) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(ms.vehicle_number) like ? ', ['%' . trim(mb_strtolower($username_phone, 'UTF-8')) . '%']);
            });
        }

        if ($manufacture != '' && $manufacture != null) {
            $result->where('ms.vehicle_manufacture_id', '=', $manufacture);
        }
        if ($model != '' && $model != null) {
            $result->where('ms.vehicle_model_id', '=', $model);
        }
        if ($code != '' && $code != null) {
            $result->where('ms.code', 'like', '%' . $code . '%');
        }
        if ($status != '' && $status != null) {
            $result->where('ms.status', '=', $status);
        } else {
            $result->where('ms.status', '<>', EStatus::DELETED);
        }
        $result = $result->where([
            ['us.type', '=', EUser::TYPE_USER],
            ['us.status', '=', EStatus::ACTIVE]
        ])->orderBy('id', 'desc')->get();

        $timezone = $this->getUserTimezone();
        foreach ($result as $key => $item) {
            $getNameManufactureById = $this->getNameManufactureByIdExport($item->vehicle_manufacture_id);
            $getNameManufactureModalById = $this->getNameManufactureByIdExport($item->vehicle_model_id);

            $item->vehicle_manufacture_id = $getNameManufactureById[0]->name;
            $item->vehicle_model_id = $getNameManufactureModalById[0]->name;

            $item->status = ECardMemberType::valueToName($item->status);
        }

		return $result;
    }

    public function getManufacture() {
        $result = DB::table('category')->select('id', 'name', 'logo_path')
                ->where([
                    'status' => EStatus::ACTIVE,
                    'type' => EManufacture::MANUFACTURE,
                ])->orderBy('seq', 'asc')->get();
        return $result;
    }

    public function getManufactureModal($id_manufacture) {
        $result = DB::table('category')->select('id', 'name', 'logo_path')
                ->where([
                    'status' => EStatus::ACTIVE,
                    'type' => EManufacture::MANUFACTURE_MODEL,
                    'parent_category_id' => $id_manufacture,
                ])->orderBy('seq', 'asc')->get();
        return $result;
    }

    public function getNameManufactureById($id_category) {
        $result = DB::table('category')->select('name')
                ->where(['id' => $id_category])->get();
        return $result;
    }

    public static function getManufactureModalExport($id_manufacture) {
        $result = DB::table('category')->select('id', 'name', 'logo_path')
                ->where([
                    'status' => EStatus::ACTIVE,
                    'type' => EManufacture::MANUFACTURE_MODEL,
                    'parent_category_id' => $id_manufacture,
                ])->orderBy('seq', 'asc')->get();
        return $result;
    }

    public static function getNameManufactureByIdExport($id_category) {
        $result = DB::table('category')->select('name')
                ->where(['id' => $id_category])->get();
        return $result;
    }

    public function deleteCardMember($id_card_member, $deleted_by) {
        try {
            $now = Carbon::now();
            $result = DB::table('membership_card')->where('id', '=', $id_card_member)->update(['status' => EStatus::DELETED, 'deleted_by' => $deleted_by, 'deleted_at' => $now]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to delete Card member. message: " . $e->getMessage());
            return null;
        }
    }

    public function saveCodeCardMember($id_card_member, $code, $name_card, $approved_by, $approved_at) {
        try {
            $now = Carbon::now();
            $result = DB::table('membership_card')->where('id', '=', $id_card_member)
                        ->update(['code' => $code, 'status' => EStatus::ACTIVE, 'approved' => true, 'vehicle_card_status' => EStatus::ACTIVE, 'approved_by' => $approved_by, 'approved_at' => $approved_at,
                                'name' => $name_card, 'expired_at' => $now->addYear()]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to Save Code Card member. message: " . $e->getMessage());
            return null;
        }
    }

    public function updateNameCodeCardMember($id_card_member, $name_card_member, $code_card_member) {
        try {
            $result = DB::table('membership_card')->where('id', '=', $id_card_member)
                    ->update(['code' => $code_card_member, 'name' => $name_card_member]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to Update Code and Name Card member. message: " . $e->getMessage());
            return null;
        }
    }
    //Appointment
    public function getIdCustomerByPhone($numberphone_user) {
        $result = DB::table('users')->select('id')
                    ->where(['status' => EStatus::ACTIVE, 'phone' => $numberphone_user])->get();
        return $result;
    }

    public function getAppointment() {
        $result = DB::table('appointment')->where(['status' => EStatus::ACTIVE])->get();
        return $result;
    }

    public function doSearchAppointment($username_phone_number, $type_appointment, $branch, $from_date, $to_date, $page) {
        $result = DB::table('appointment as appo')
                    ->select('appo.id', 'appo.user_id', 'appo.type', 'appo.appointment_at', 'appo.branch_id', 'appo.note', 'appo.enable_reminder',
                             'appo.created_at', 'us.name as name_user', 'us.phone', 'bra.name as name_branch')
                    ->join('users as us', 'us.id', '=', 'appo.user_id')
                    ->join('branch as bra', 'bra.id', '=', 'appo.branch_id');
            
        if ($username_phone_number != '' && $username_phone_number != null) {
            $result->where(function($where) use ($username_phone_number) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($username_phone_number, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($username_phone_number, 'UTF-8')) . '%']);
              });
        }
        if ($type_appointment != '' && $type_appointment != null) {
            $result->where('appo.type', '=', $type_appointment);
        }
        if ($branch != '' && $branch != null) {
            $result->where('appo.branch_id', '=', $branch);
        } 
        if ($from_date != '' && $from_date != null) {
            $result->where('appo.appointment_at', '>', $from_date);
        }
        if ($to_date != '' && $to_date != null) {
            $result->where('appo.appointment_at', '<', $to_date);
        }
        $result = $result->where([
            ['us.status', '=', EStatus::ACTIVE],
            ['appo.status', '=', EStatus::ACTIVE]
        ])->orderBy('id', 'desc')->paginate(15);
		return $result;
    }
    
    public function saveAppointment($user_id, $branch, $typeAppointment, $appointment_at, $note, $reminder, $created_by) {
        try {
            $now = Carbon::now();
            $appointment = new Appointment();
            $appointment->user_id = $user_id;
            $appointment->type = $typeAppointment;
            $appointment->status = EStatus::ACTIVE;
            $appointment->appointment_at = $appointment_at;
            $appointment->branch_id = $branch;
            $appointment->note = $note;
            $appointment->enable_reminder = $reminder;
            $appointment->created_at = $now;
            $appointment->created_by = $created_by;
            $appointment->save();
            return $appointment;
        } catch (\Exception $e) {
            logger('Fail Save appointment ' . $user_id . $appointment_at, ['e' => $e]);
        }
    }

    public function updateAppointment($user_id, $branch, $typeAppointment, $appointment_at, $note, $reminder, $created_by, $id_appointment) {
        try {
            $result = DB::table('appointment')->where([['id', '=', $id_appointment]])
                        ->update([  'user_id' => $user_id,
                                    'branch_id' => $branch,
                                    'type' => $typeAppointment,
                                    'appointment_at' => $appointment_at,
                                    'enable_reminder' => $reminder,
                                    'note' => $note,
                                    'created_by' => $created_by]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to update appointment. " . $user_id . " message: " . $e->getMessage());
            return null;
        }
    }

    public function deleteAppointmentSchedule($id_appointment) {
        try {
            $result = DB::table('appointment')->where([['id', '=', $id_appointment]])
                        ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to delete appointment. message: " . $e->getMessage());
            return null;
        }
    }


    public function getStaffUsers() {
        try {
            $result = DB::table('category')->select('id', 'name')->where('type', '=', EUser::TYPE_STAFF_SYNC)->get();
            return $result;
        } catch (Exception $e) {
            logger("Failed to get getStaffUsers. message:". $e->getMessage());
            return null;
        }
    }

    public function getPartnerName($partner_id) {
        try {
            $result = DB::table('category')->select('id','name')->where('id', '=', $partner_id)->where('status', '=', EStatus::ACTIVE)->get();
            return $result;
        } catch (Exception $e) {
            logger("Failed to get getPartnerName. message:". $e->getMessage());
            return null;    
        }
        
    }

    public function getPartnerField() {
        try {
            $result = DB::table('category')->select('id', 'name')->where('type', '=', EPartnerType::PRODUCT_PARTNERFIELD)->get();
            return $result;
        } catch (Exception $e) {
            logger("Failed to get getPartnerField. message:". $e->getMessage());
            return null;
        }
    }


    public function getPartner($partner_id) {
        try {
            $result = DB::table('category')->select('id', 'name', 'parent_category_id')->where('type', '=', EPartnerType::PRODUCT_PARTNER)->where('parent_category_id', '=', $partner_id)->get();
            return $result;
        } catch (Exception $e) {
            logger("Failed to get partner. message:". $e->getMessage());
            return null;
        }
    }

    public static function getPartnerNameExcel($partner_id) {
        try {
            $result = DB::table('category')->select('id','name')->where('id', '=', $partner_id)->get();
            return $result;
        } catch (Exception $e) {
            logger("Failed to get getPartnerNameExcel. message:". $e->getMessage());
            return null;    
        }
        
    }

    public function editAddressUser($id_user_address, $address_edit) {
        try {
            $result = DB::table('user_address')->where([['user_id', '=', $id_user_address],['is_default', '=', true]])
                        ->update(['address' => $address_edit,
                                ]);
            return $result;
        } catch (Exception $e) {
            logger("Failed to get editAddressUser. message:". $e->getMessage());
            return null;
        }
    }

    public function getPartnerParentId($partner_id) {
        try {
            $result = DB::table('category')->select('id', 'name', 'parent_category_id')->where('type', '=', EPartnerType::PRODUCT_PARTNER)->where('id', '=', $partner_id)->get();
            return $result;
        } catch (Exception $e) {
            logger("Failed to get partner. getPartnerParentId:". $e->getMessage());
            return null;
        }
    }
}