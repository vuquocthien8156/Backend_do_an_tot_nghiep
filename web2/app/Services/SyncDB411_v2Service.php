<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Enums\EUser;
use App\Enums\EOrderType;
use App\Constant\SessionKey;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Repositories\SyncDB411_v2Repository;

class SyncDB411_v2Service {
    protected $syncDB411_v2Repository;

	public function __construct(SyncDB411_v2Repository $syncDB411_v2Repository) {
		$this->syncDB411_v2Repository = $syncDB411_v2Repository;
    }

    public function getAllDataSyncUser_v2($pageSize, $page) {
        return $this->syncDB411_v2Repository->getAllDataSyncUser_v2($pageSize, $page);
    }

    public function getAllDataSyncUser2_v2($pageSize, $page) {
        return $this->syncDB411_v2Repository->getAllDataSyncUser2_v2($pageSize, $page);
    }

    public function checkUserExist_MaKH($value) {
        return $this->syncDB411_v2Repository->checkUserExist_MaKH($value);
    }

    public function updateUser($id_user, $id_sync_user, $name, $phone, $email, $address, $meta) {
        $timestamp = Carbon::now();
        try {
            $updateUser = $this->syncDB411_v2Repository->updateUser($id_user, $id_sync_user, $name, $phone, $email, $address, $meta);
            if (isset($updateUser)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update user id_user: {$id_user}";
                Storage::append("error_sync_v2/error_user_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed update update user id_user: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_v2/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }


    public function getAllDataUpdateMembershipCard_v2($pageSize, $page) {
        return $this->syncDB411_v2Repository->getAllDataUpdateMembershipCard_v2($pageSize, $page);
    }

    public function checkMemberShipCardExist_MaXe($value) {
        return $this->syncDB411_v2Repository->checkMemberShipCardExist_MaXe($value);
    }

    public function updateMembershipCard($id_membership_card, $code, $number_vehicle, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $meta_new) {
        $timestamp = Carbon::now();
        try {
            $updateMembershipCard =  $this->syncDB411_v2Repository->updateMembershipCard($id_membership_card, $code, $number_vehicle, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $meta_new);
            if (isset($updateMembershipCard)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update membership card id_membership_card: {$id_membership_card}";
                Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed update membership card id_membership_card: {$id_membership_card} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function getAllDataSyncStaff_v2($pageSize, $page) {
        return $this->syncDB411_v2Repository->getAllDataSyncStaff_v2($pageSize, $page);
    }

    //DELETE

    // Staff
    public function getAllDataStaff_Delete_v2($pageSize, $page) {
        return $this->syncDB411_v2Repository->getAllDataStaff_Delete_v2($pageSize, $page);
    }

    public function checkExist_delete_MaNV($value) {
        return $this->syncDB411_v2Repository->checkExist_delete_MaNV($value);
    }

    public function updateMetaUser($id_user, $myJSON) {
        return $this->syncDB411_v2Repository->updateMetaUser($id_user, $myJSON);
    }

    public function deteleUserById($id_user) {
        $timestamp = Carbon::now();
        try {
            $deteleUserById =  $this->syncDB411_v2Repository->deteleUserById($id_user);
            if (isset($deteleUserById)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! delete User id_user: {$id_user}";
                Storage::append("error_sync_v2/error_user_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed delete user id_user: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_v2/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function deteleBranchStaffById($id_user) {
        $timestamp = Carbon::now();
        try {
            $deteleBranchStaffById =  $this->syncDB411_v2Repository->deteleBranchStaffById($id_user);
            if (isset($deteleBranchStaffById)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! delete branch staff id_user: {$id_user}";
                Storage::append("error_sync_v2/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed delete branch staff id_user: {$id_user} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_v2/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function getAllDataUser_Delete_v2($pageSize, $page) {
        return $this->syncDB411_v2Repository->getAllDataUser_Delete_v2($pageSize, $page);
    }

    //User
    public function checkExist_delete_MaKH_KhachHang($value) {
        return $this->syncDB411_v2Repository->checkExist_delete_MaKH_KhachHang($value);
    }

    public function checkExist_delete_MaKH_KhachHangNgoai($value) {
        return $this->syncDB411_v2Repository->checkExist_delete_MaKH_KhachHangNgoai($value);
    }

    //membership card

    public function getAllDataMemberShip_Card_Delete_v2($pageSize, $page) {
        return $this->syncDB411_v2Repository->getAllDataMemberShip_Card_Delete_v2($pageSize, $page);
    }

    public function checkExist_delete_MaXe($value) {
        return $this->syncDB411_v2Repository->checkExist_delete_MaXe($value);
    }

    public function updateMetaMemberShipCard($id_membership_card, $myJSON) {
        $timestamp = Carbon::now();
        try {
            $updateMetaMemberShipCard =  $this->syncDB411_v2Repository->updateMetaMemberShipCard($id_membership_card, $myJSON);
            if (isset($updateMetaMemberShipCard)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! update meta id_membership_card: {$id_membership_card}";
                Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed update meta id_membership_card: {$id_membership_card} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error

        }
    }

    public function deteleMemberShipCardById($id_membership_card) {
        $timestamp = Carbon::now();
        try {
            $deteleMemberShipCardById = $this->syncDB411_v2Repository->deteleMemberShipCardById($id_membership_card);
            if (isset($deteleMemberShipCardById)) {
                $content_success = "{$timestamp->format('Y:m:d H:i:s')}: => Success! delete card member id_membership_card: {$id_membership_card}";
                Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_success); //Error
            }
        } catch (Exception $e) {
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed delete card member  id_membership_card: {$id_membership_card} . Error:  {$e->getMessage()}";
            Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }
}
