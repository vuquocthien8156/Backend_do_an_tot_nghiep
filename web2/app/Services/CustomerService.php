<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;

class CustomerService {
    protected $customerRepository;

	public function __construct(CustomerRepository $customerRepository) {
		$this->customerRepository = $customerRepository;
    }
    
    public function doSearchCustomer($username_phone, $status, $from_date, $to_date, $partner_id, $page = 1, $pageSize = 15) {
        return $this->customerRepository->doSearchCustomer($username_phone, $status, $from_date, $to_date, $partner_id, $page = 1, $pageSize = 15);
    }

    public function doExportExelCustomer($username_phone, $status, $from_date, $to_date) {
        return $this->customerRepository->doExportExelCustomer($username_phone, $status, $from_date, $to_date);
    }

    public function getFullCustomer() {
        return $this->customerRepository->getFullCustomer();
    }

    public function doSearchCardMember($username_phone, $manufacture, $model, $code, $status, $approved, $vehicle_card_status, $page = 1, $pageSize = 15) {
        return $this->customerRepository->doSearchCardMember($username_phone, $manufacture, $model, $code, $status, $approved, $vehicle_card_status, $page = 1, $pageSize = 15);
    }

    public function doExportExelCardMember($username_phone, $manufacture, $model, $code, $status) {
        return $this->customerRepository->doExportExelCardMember($username_phone, $manufacture, $model, $code, $status);
    }

    public function deleteCustomer($id_customer) {
        return $this->customerRepository->deleteCustomer($id_customer);
    }

    public function saveUpgradeUser($id_user, $name, $phone, $email, $avatar_path, $created_by, $type_employees) {
        return $this->customerRepository->saveUpgradeUser($id_user, $name, $phone, $email, $avatar_path, $created_by, $type_employees);
    }

    public function saveStaffBranch($branch_id, $id_user, $created_by) {
        return $this->customerRepository->saveStaffBranch($branch_id, $id_user, $created_by);
    }

    public function getManufacture() {
        return $this->customerRepository->getManufacture();
    }

    public function getManufactureModal($id_manufacture) {
        return $this->customerRepository->getManufactureModal($id_manufacture);
    }

    public function getNameManufactureById($id_category) {
        return $this->customerRepository->getNameManufactureById($id_category);
    }

    public function deleteCardMember($id_card_member, $deleted_by) {
        return $this->customerRepository->deleteCardMember($id_card_member, $deleted_by);
    }

    public function saveCodeCardMember($id_card_member, $code, $name_card, $approved_by, $approved_at) {
        return $this->customerRepository->saveCodeCardMember($id_card_member, $code, $name_card, $approved_by, $approved_at);
    }

    public function updateNameCodeCardMember($id_card_member, $name_card_member, $code_card_member) {
        return $this->customerRepository->updateNameCodeCardMember($id_card_member, $name_card_member, $code_card_member);
    }

    //Appointment
    public function getIdCustomerByPhone($numberphone_user) {
        return $this->customerRepository->getIdCustomerByPhone($numberphone_user);
    }

    public function doSearchAppointment($username_phone_number, $type_appointment, $branch, $from_date, $to_date, $page) {
        return $this->customerRepository->doSearchAppointment($username_phone_number, $type_appointment, $branch, $from_date, $to_date, $page);
    }

    public function saveOrUpdateAppointment($user_id, $branch, $typeAppointment, $appointment_at, $note, $reminder, $created_by, $id_appointment) {
        if ($id_appointment === null) {
            return $this->customerRepository->saveAppointment($user_id, $branch, $typeAppointment, $appointment_at, $note, $reminder, $created_by);
        } else {
            return $this->customerRepository->updateAppointment($user_id, $branch, $typeAppointment, $appointment_at, $note, $reminder, $created_by, $id_appointment);
        }
    }

    public function deleteAppointmentSchedule($id_appointment) {
        return $this->customerRepository->deleteAppointmentSchedule($id_appointment);
    }

    public function EditCustomerUser($id_user_edit, $name_edit, $phone_edit, $birthday_edit, $partnerfield_edit, $address_edit, $updated_by, $updated_at, $avatar_path) {
        return $this->customerRepository->EditCustomerUser($id_user_edit, $name_edit, $phone_edit, $birthday_edit, $partnerfield_edit, $address_edit, $updated_by, $updated_at, $avatar_path);
    }

    public function getStaffUsers() {
        return $this->customerRepository->getStaffUsers();
    }

    public function getPartnerName($partner_id) {
        return $this->customerRepository->getPartnerName($partner_id);
    }

    public function getPartnerField() {
        return $this->customerRepository->getPartnerField();
    }

    public function getPartner($partner_id) {
        return $this->customerRepository->getPartner($partner_id);
    }

    public static function getPartnerNameExcel($partner_id) {
        return CustomerRepository::getPartnerNameExcel($partner_id);
    }

    public function editAddressUser($id_user_address, $address_edit) {
        return $this->customerRepository->editAddressUser($id_user_address, $address_edit);
    }

    public function getPartnerParentId($partner_id) {
        return $this->customerRepository->getPartnerParentId($partner_id);
    }

}