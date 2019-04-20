<?php

namespace App\Console\Commands;

use App\Constant\ConfigKey;
use App\Enums\ENotificationType;
use App\Enums\ENotificationScheduleType;
use App\Enums\EStatus;
use App\Enums\EAppointmentType;
use App\Enums\EDateFormat;
use App\Enums\EManufacture;
use App\Enums\EUser;
use App\Enums\EOrderType;
use App\Helpers\ConfigHelper;
use App\Traits\CommonTrait;
use App\Services\SyncDB411_v2Service;
use App\Services\SyncDB411Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SyncDB411_v2 extends Command {
    use CommonTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_v2:411 
                            {--sync_v2-all : Sync All Data DB 411}
                            {--sync_v2-staff : Sync Staff}
                            {--sync_v2-membership-card : Sync User}
                            {--sync_v2-order : Sync Order}
                            {--sync_v2-user2 : Sync User2 Table: dbo.KhachHang}
                            {--sync_v2-user1 : Sync User1 Table: dbo.KhachHangNgoai}
                            {--sync_v2-update-membership-card : Sync Update MemberShip Card Table: dbo.kmTheVIP}
                            {--sync_v2-test-function : Sync Test Function}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct(SyncDB411_v2Service $syncDB411_v2Service, SyncDB411Service $syncDB411Service) {
        parent::__construct();
        $this->syncDB411_v2Service = $syncDB411_v2Service;
        $this->syncDB411Service = $syncDB411Service;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle() { 
        if ($this->option('sync_v2-all')) {
            $this->syncUpdateUser_v2();
            $this->syncUpdateUser2_v2();
            $this->syncUpdateStaff_v2();
            $this->sync_UpdateMemberShipCard_v2();
            $this->sync_DeteleUser_v2();
            $this->sync_DeleteStaff_v2();
            $this->sync_DeleteMembership_Card_v2();
        }

        if ($this->option('sync_v2-test-function')) {
           $this->sync_DeteleUser_v2();
        }
    }

    public function syncUpdateUser_v2() { // dbo.KhachHangNgoai
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncUser_v2 = $this->syncDB411_v2Service->getAllDataSyncUser_v2($pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncUser_v2->lastPage();
            }
            if (count($syncUser_v2) > 0) {
                foreach ($syncUser_v2 as $key => $value) {
                    $myObj = (object)[];
                    $myObj2 = (object)[];
                    $myObj3 = (object)[];
                    $myObj->MaKH = $value->MaKH;
                    $myObj->HoTen = $value->HoTen;
                    $myObj->DTDiDong = $value->DTDiDong;
                    $myObj->EmailKH = $value->EmailKH;
                    $myObj->DiaChi = $value->DiaChi;
                    $myObj->MaHieuKH = $value->MaHieuKH;
                    $myObj2->hash_v2 = hash('sha256', serialize($myObj));
                    $myObj2->data_v2 = $myObj;
                    $myObj3->syncData = array($myObj2);
                    $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $checkUserExist = $this->syncDB411_v2Service->checkUserExist_MaKH($value->MaKH);
                    if(isset($checkUserExist[0]->id)) {
                        $id_user = $checkUserExist[0]->id;
                        $meta_old = $checkUserExist[0]->meta;
                        $myJson_Old = json_decode($meta_old, true);
                        array_push($myJson_Old['syncData'], $myObj2);
                        $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        $this->syncDB411_v2Service->updateUser($id_user, $value->MaKH, $value->HoTen, preg_replace('/\D/', '', $value->DTDiDong), $value->EmailKH, $value->DiaChi, $meta_new);


                    } else {
                        if ($value->DTDiDong != null && preg_replace('/\D/', '', $value->DTDiDong) != "") {
                            $check_phone_exist = $this->syncDB411Service->checkPhoneExist(preg_replace('/\D/', '', $value->DTDiDong), $type = EUser::TYPE_USER);
                            if (isset($check_phone_exist[0]->id)) {
                                $id_user = $check_phone_exist[0]->id;
                                $meta_old = $check_phone_exist[0]->meta;
                                if ($meta_old == null) {
                                    $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $myJSON, $type = null, $staff_type_id = null);
                                } else {
                                    $myJson_Old = json_decode($meta_old, true);
                                    array_push($myJson_Old['syncData'], $myObj2);
                                    $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                    $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $meta_new, $type = null, $staff_type_id = null);
                                }
                            } else {
                                $this->syncDB411Service->saveUserSync($value->MaKH, $value->HoTen, preg_replace('/\D/', '', $value->DTDiDong), $value->EmailKH, $value->DiaChi, $myJSON);
                            }
                        } else {
                            $this->syncDB411Service->saveUserSync($value->MaKH, $value->HoTen, preg_replace('/\D/', '', $value->DTDiDong), $value->EmailKH, $value->DiaChi, $myJSON);
                        }
                    }
                }
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function syncUpdateStaff_v2() {
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncStaff_v2 = $this->syncDB411_v2Service->getAllDataSyncStaff_v2($pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncStaff_v2->lastPage();
            }
            if (count($syncStaff_v2) > 0) {
                foreach ($syncStaff_v2 as $key => $value) {
                    $myObj = (object)[];
                    $myObj2 = (object)[];
                    $myObj3 = (object)[];
                    $myObj->MaNV = $value->MaNV;
                    $myObj->HoTenNV = $value->HoTenNV;
                    $myObj->Mobile = $value->Mobile;
                    $myObj->DiaChi = $value->DiaChi;
                    $myObj->CMND = $value->CMND;
                    $myObj->MaCuaHang = $value->MaCuaHang;
                    $myObj->MaCV = $value->MaCV;
                    $myObj->MaBoPhan = $value->MaBoPhan;
                    $myObj->MaNhomNhanVien = $value->MaNhomNhanVien;
                    $myObj2->hash_v2 = hash('sha256', serialize($myObj));
                    $myObj2->data_v2 = $myObj;
                    $myObj3->syncData = array($myObj2);
                    $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $branch_data_id = $this->syncDB411Service->getIdBranchByDataSync($value->MaCuaHang);
                    $type_staff_data_id = $this->syncDB411Service->getTypeStaffIdByDataSync($value->MaNhomNhanVien);
                    $data_user_staff_id = $this->syncDB411Service->getIdTypeStaffByDataSync($value->MaNV);
                    $branch_id = isset($branch_data_id[0]->id) ? $branch_data_id[0]->id : null;
                    $staff_type_id = isset($type_staff_data_id[0]->id) ? $type_staff_data_id[0]->id : null;
                    if (!isset($data_user_staff_id[0]->id)) {
                        if ($value->Mobile != null && preg_replace('/\D/', '', $value->Mobile) != "") {
                            $check_phone_exist = $this->syncDB411Service->checkPhoneExist(preg_replace('/\D/', '', $value->Mobile), $type = EUser::TYPE_STAFF);
                            if (isset($check_phone_exist[0]->id)) {
                                $id_user = $check_phone_exist[0]->id;
                                $meta_old = $check_phone_exist[0]->meta;
                                if ($meta_old == null) {
                                    $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $myJSON, $type, $staff_type_id);
                                } else {
                                    $myJson_Old = json_decode($meta_old, true);
                                    array_push($myJson_Old['syncData'], $myObj2);
                                    $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                    $type = EUser::TYPE_STAFF;
                                    $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $meta_new, $type, $staff_type_id);
                                }
                            } else {
                                $this->syncDB411Service->saveStaffSync($value->MaNV, $value->HoTenNV, preg_replace('/\D/', '', $value->Mobile), $value->Email, $value->DiaChi, $branch_id, $staff_type_id, $myJSON);
                            }
                        } else {
                            $this->syncDB411Service->saveStaffSync($value->MaNV, $value->HoTenNV, preg_replace('/\D/', '', $value->Mobile), $value->Email, $value->DiaChi, $branch_id, $staff_type_id, $myJSON);
                        }
                    } else {
                        $type_staff_data_id_2 = $this->syncDB411Service->getTypeStaffIdByDataSync($val = 2);
                        $staff_type_id_2 = isset($type_staff_data_id_2[0]->id) ? $type_staff_data_id_2[0]->id : null;
                        $this->syncDB411Service->updateTypeStaff($data_user_staff_id[0]->id, $staff_type_id_2);
                    }
                }
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function syncUpdateUser2_v2() { // dbo.KhachHangNgoai
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncUser2_v2 = $this->syncDB411_v2Service->getAllDataSyncUser2_v2($pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncUser2_v2->lastPage();
            }
            if (count($syncUser2_v2) > 0) {
                foreach ($syncUser2_v2 as $key => $value) {
                    $myObj = (object)[];
                    $myObj2 = (object)[];
                    $myObj3 = (object)[];
                    $myObj->MaKH = $value->MaKH;
                    $myObj->HoTenKH = $value->HoTenKH;
                    $myObj->DTKH = $value->DTKH;
                    $myObj->EmailKH = $value->EmailKH;
                    $myObj->DiaChiKH = $value->DiaChiKH;
                    $myObj2->hash_v2 = hash('sha256', serialize($myObj));
                    $myObj2->data_v2 = $myObj;
                    $myObj3->syncData = array($myObj2);
                    $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $checkUserExist = $this->syncDB411_v2Service->checkUserExist_MaKH($value->MaKH);
                    if(isset($checkUserExist[0]->id)) {
                        $id_user = $checkUserExist[0]->id;
                        $meta_old = $checkUserExist[0]->meta;
                        $myJson_Old = json_decode($meta_old, true);
                        array_push($myJson_Old['syncData'], $myObj2);
                        $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        $this->syncDB411_v2Service->updateUser($id_user, $value->MaKH, $value->HoTenKH, preg_replace('/\D/', '', $value->DTKH), $value->EmailKH, $value->DiaChiKH, $meta_new);

                    } else {
                        if ($value->DTKH != null && preg_replace('/\D/', '', $value->DTKH) != "") {
                            $check_phone_exist = $this->syncDB411Service->checkPhoneExist(preg_replace('/\D/', '', $value->DTKH), $type = EUser::TYPE_USER);
                            if (isset($check_phone_exist[0]->id)) {
                                $id_user = $check_phone_exist[0]->id;
                                $meta_old = $check_phone_exist[0]->meta;
                                if ($meta_old == null) {
                                    $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $myJSON, $type = null, $staff_type_id = null);
                                } else {
                                    $myJson_Old = json_decode($meta_old, true);
                                    array_push($myJson_Old['syncData'], $myObj2);
                                    $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                    $this->syncDB411Service->updateMetaIfPhoneSame($id_user, $meta_new, $type = null, $staff_type_id = null);
                                }
                            } else {
                                $this->syncDB411Service->saveUserSync($value->MaKH, $value->HoTenKH, preg_replace('/\D/', '', $value->DTKH), $value->EmailKH, $value->DiaChiKH, $myJSON);
                            }
                        } else {
                            $this->syncDB411Service->saveUserSync($value->MaKH, $value->HoTenKH, preg_replace('/\D/', '', $value->DTKH), $value->EmailKH, $value->DiaChiKH, $myJSON);
                        }
                    }
                }
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function sync_UpdateMemberShipCard_v2() {
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncMemberShipCard_v2 = $this->syncDB411_v2Service->getAllDataUpdateMembershipCard_v2($pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncMemberShipCard_v2->lastPage();
            }
            if (count($syncMemberShipCard_v2) > 0) {
                foreach ($syncMemberShipCard_v2 as $key => $value) {
                    $myObj = (object)[];
                    $myObj2 = (object)[];
                    $myObj3 = (object)[];
                    $myObj->ID = $value->ID;
                    $myObj->SoThe = $value->SoThe;
                    $myObj->MaVach = $value->MaVach;
                    $myObj->NgayPH = $value->NgayPH;
                    $myObj->NgayHL = $value->NgayHL;
                    $myObj->NgayHH = $value->NgayHH;
                    $myObj->MaKH = $value->MaKH;
                    $myObj->NgayNhap = $value->NgayNhap;
                    $myObj->TienThe = $value->TienThe;
                    $myObj->DienGiai = $value->DienGiai;
                    $myObj->MaXe = $value->MaXe;
                    $myObj->BienSoXe = $value->BienSoXe;
                    $myObj->SoMay = $value->SoMay;
                    $myObj->SoKhung = $value->SoKhung;
                    $myObj->DongXe = $value->DongXe;
                    $myObj->SoBaoHanh = $value->SoBaoHanh;
                    $myObj->MaLoai = $value->MaLoai;
                    $myObj->MauXe = $value->MauXe;
                    $myObj2->hash_v2 = hash('sha256', serialize($myObj));
                    $myObj2->data_v2 = $myObj;
                    $myObj3->syncData = array($myObj2);
                    $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $id_user_data = $this->syncDB411Service->getIdUserByDataSync($value->MaKH);
                    $checkMembershipCardExist = $this->syncDB411_v2Service->checkMemberShipCardExist_MaXe($value->MaXe);
                    $id_manufacture_model_data = $this->syncDB411Service->getIdManufactureModelByDataSync($value->MaLoai);
                    $id_manufacture = isset($id_manufacture_model_data[0]->parent_category_id) ? $id_manufacture_model_data[0]->parent_category_id : null;
                    $id_model = isset($id_manufacture_model_data[0]->id) ? $id_manufacture_model_data[0]->id : null;
                    if ($value->MaVach != null) {
                        $approved = true;
                        $vehicle_card_status = 1;
                    } else {
                        $approved = false;
                        $vehicle_card_status = 2;
                    }
                    $created_at = ($value->NgayPH != null) ? Carbon::parse($value->NgayPH) : null;
                    $approved_at = ($value->NgayHL != null) ? Carbon::parse($value->NgayHL) : null;
                    $expired_at = ($value->NgayHH != null) ? Carbon::parse($value->NgayHH) : null;
                    
                    if(isset($checkMembershipCardExist[0]->id)) {
                        $id_membership_card = $checkMembershipCardExist[0]->id;
                        $meta_old = $checkMembershipCardExist[0]->meta;
                        $myJson_Old = json_decode($meta_old, true);
                        array_push($myJson_Old['syncData'], $myObj2);
                        $meta_new = json_encode($myJson_Old, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        $this->syncDB411_v2Service->updateMembershipCard($id_membership_card, $value->MaVach, $value->BienSoXe, $id_manufacture, 
                                                                        $id_model, $value->MauXe, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $meta_new);

                    } else {
                        $id_user = isset($id_user_data[0]->id) ? $id_user_data[0]->id : null;
                        $name = isset($id_user_data[0]->name) ? $id_user_data[0]->name : null;
                        $check_user_id_exist = $this->syncDB411Service->checkUserIdMembershipCard($id_user);
                        $status = isset($check_user_id_exist[0]->id) ? 2 : EStatus::ACTIVE;
                        
                        $this->syncDB411Service->saveMemberShipCardSync($id_user, $status, $name, $value->BienSoXe, $value->MaVach, $id_manufacture, $id_model, $value->MauXe, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $myJSON);

                    }
                }
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function sync_DeleteStaff_v2() {
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncDeleteStaff_v2 = $this->syncDB411_v2Service->getAllDataStaff_Delete_v2($pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncDeleteStaff_v2->lastPage();
            }
            if (count($syncDeleteStaff_v2) > 0) {
                foreach ($syncDeleteStaff_v2 as $key1 => $value) {
                    $id_user = $value->id;
                    $myJson_Old = json_decode($value->meta, true);
                    foreach ($myJson_Old as $key2 => $items) {
                        $arr_ = [];
                        $tmp = false;
                        foreach ($items as $key3 => $item) {
                            if(isset($item['data']['MaNV'])) {
                                $tmp = true;
                                $checkExist_delete_MaNV = $this->syncDB411_v2Service->checkExist_delete_MaNV($item['data']['MaNV']);
                                if (!isset($checkExist_delete_MaNV[0]->MaNV)) {
                                    unset($items[$key3]);
                                } else {
                                    array_push($arr_, $items[$key3]);
                                }
                            }
                        }
                        if ($tmp == true) {
                            if (count($arr_) > 0) {
                                $myObj3 = (object)[];
                                $myObj3->syncData = $arr_;
                                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                $this->syncDB411_v2Service->updateMetaUser($id_user, $myJSON);
                            } else {
                                $this->syncDB411_v2Service->deteleUserById($id_user);
                                $this->syncDB411_v2Service->deteleBranchStaffById($id_user);
                            }
                        }
                    }
                }
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function sync_DeteleUser_v2() {
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncDeleteUser_v2 = $this->syncDB411_v2Service->getAllDataUser_Delete_v2($pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncDeleteUser_v2->lastPage();
            }
            if (count($syncDeleteUser_v2) > 0) {
                foreach ($syncDeleteUser_v2 as $key1 => $value) {
                    $id_user = $value->id;
                    $myJson_Old = json_decode($value->meta, true);
                    foreach ($myJson_Old as $key2 => $items) {
                        $arr_ = [];
                        $tmp = false;
                        foreach ($items as $key3 => $item) {
                            if(isset($item['data']['MaKH'])) {
                                $tmp = true;
                                $checkExist_delete_MaKH_KhachHang = $this->syncDB411_v2Service->checkExist_delete_MaKH_KhachHang($item['data']['MaKH']);
                                $checkExist_delete_MaKH_KhachHangNgoai = $this->syncDB411_v2Service->checkExist_delete_MaKH_KhachHangNgoai($item['data']['MaKH']);
                                if (!isset($checkExist_delete_MaKH_KhachHang[0]->MaKH) && !isset($checkExist_delete_MaKH_KhachHangNgoai[0]->MaKH)) {
                                    unset($items[$key3]);
                                } else {
                                    array_push($arr_, $items[$key3]);
                                }
                            }
                        }
                        if ($tmp == true) {
                            if (count($arr_) > 0) {
                                $myObj3 = (object)[];
                                $myObj3->syncData = $arr_;
                                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                $this->syncDB411_v2Service->updateMetaUser($id_user, $myJSON);
                            } else {
                                $this->syncDB411_v2Service->deteleUserById($id_user);
                            }
                        }
                    }
                }
            }
            $page += 1;
        } while ($page <= $lastPage);
    }

    public function sync_DeleteMembership_Card_v2() {
        $page = 1;
        $pageSize = 1000;
        $lastPage = null;
        do {
            $syncDeleteMemberShipCard_v2 = $this->syncDB411_v2Service->getAllDataMemberShip_Card_Delete_v2($pageSize, $page);
            if ($lastPage == null) {
                $lastPage = $syncDeleteMemberShipCard_v2->lastPage();
            }
            if (count($syncDeleteMemberShipCard_v2) > 0) {
                foreach ($syncDeleteMemberShipCard_v2 as $key1 => $value) {
                    $id_membership_card = $value->id;
                    $myJson_Old = json_decode($value->meta, true);
                    foreach ($myJson_Old as $key2 => $items) {
                        $arr_ = [];
                        $tmp = false;
                        foreach ($items as $key3 => $item) {
                            if(isset($item['data']['MaXe'])) {
                                $tmp = true;
                                $checkExist_delete_MaXe= $this->syncDB411_v2Service->checkExist_delete_MaXe($item['data']['MaXe']);
                                if (!isset($checkExist_delete_MaXe[0]->MaXe)) {
                                    unset($items[$key3]);
                                } else {
                                    array_push($arr_, $items[$key3]);
                                }
                            }
                        }
                        if ($tmp == true) {
                            if (count($arr_) > 0) {
                                $myObj3 = (object)[];
                                $myObj3->syncData = $arr_;
                                $myJSON = json_encode($myObj3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                $this->syncDB411_v2Service->updateMetaMemberShipCard($id_membership_card, $myJSON);
                            } else {
                                $this->syncDB411_v2Service->deteleMemberShipCardById($id_membership_card);
                            }
                        }
                    }
                }
            }
            $page += 1;
        } while ($page <= $lastPage);
    }
}