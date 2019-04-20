<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EVehicleType;
use App\Enums\EOrderType;
use App\Enums\EOrderStatus;
use App\Enums\EManufacture;
use App\Enums\ECategoryType;
use App\Models\Users;
use App\Models\BranchStaff;
use App\Models\MemberShipCard;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Branch;
use App\Models\AppConfig;
use App\Models\UserAddress;
use App\Models\Category;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class SyncDB411_v2Repository {
    public function __construct(Users $users, BranchStaff $branchStaff, MemberShipCard $memberShipCard, UserAddress $userAddress,
                                Order $order, OrderDetail $orderDetail, Branch $branch, AppConfig $appConfig, Category $category) {
        $this->users = $users;
        $this->branchStaff = $branchStaff;
        $this->memberShipCard = $memberShipCard;
        $this->order = $order;
        $this->orderDetail = $orderDetail;
        $this->branch = $branch;
        $this->appConfig = $appConfig;
        $this->userAddress = $userAddress;
        $this->category = $category;
    }

    public function getAllDataSyncUser_v2($pageSize, $page) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.KhachHang')
                        ->select('MaKH', 'HoTen', 'DiaChi', 'DTDiDong', 'MaHieuKH', 'EmailKH');
            
            $query = $result->orderBy('MaKH', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);
            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user sync table: dbo.KhachHang. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getAllDataSyncUser2_v2($pageSize, $page) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.KhachHangNgoai')
                        ->select('MaKH', 'HoTenKH', 'DTKH', 'EmailKH', 'DiaChiKH');
            
            $query = $result->orderBy('MaKH', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);
            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user sync table: dbo.KhachHangNgoai. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkUserExist_MaKH($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaKH\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users as us')
                        ->select('us.id', 'us.name', 'us.meta')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id users, MaKH: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateUser($id_user, $id_sync_user, $name, $phone, $email, $address, $meta) {
        try {
            $now = Carbon::now();
            $user = Users::where('id', '=', $id_user)
                            ->update([  'name' => $name,
                                        'phone' => $phone, 
                                        'email' => $email, 
                                        'address' => $address, 
                                        'meta' => $meta]);
            return $user;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Update User id_user: {$id_user}.  Error:  {$e->getMessage()}";
            Storage::append("error_sync_v2/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getAllDataUpdateMembershipCard_v2($pageSize, $page) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.XeMuaCHKhac as xmchk')
                    ->select('kmtv.ID', 'kmtv.SoThe', 'kmtv.MaVach', 'kmtv.NgayPH', 'kmtv.NgayHL', 'kmtv.NgayHH', 'xmchk.MaKH', 
                            'kmtv.NgayNhap', 'kmtv.TienThe', 'kmtv.DienGiai', 
                            'xmchk.MaXe', 'xmchk.BienSoXe', 'xmchk.SoMay', 'xmchk.SoKhung', 'xmchk.DongXe', 'xmchk.SoBaoHanh', 'xmchk.MaLoai', 'mx.TenMau as MauXe')
                    ->leftJoin('dbo.kmTheVIP as kmtv', 'kmtv.MaKH', '=', 'xmchk.MaKH')
                    ->join('dbo.MauXe as mx', 'mx.Khoa', '=', 'xmchk.MaMau');
            $query = $result->orderBy('xmchk.MaXe', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user sync table: dbo.XeMuaCHKhac. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkMemberShipCardExist_MaXe($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaXe\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('membership_card')
                        ->select('id', 'user_id', 'meta')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id users, MaKH: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateMembershipCard($id_membership_card, $code, $number_vehicle, $id_manufacture, $id_model, $color, $created_at, $approved_at, $expired_at, $approved, $vehicle_card_status, $meta_new) {
        try {
            $now = Carbon::now();
            $user = MemberShipCard::where('id', '=', $id_membership_card)
                            ->update([  'code' => $code, 'vehicle_number' => $number_vehicle, 
                                        'vehicle_manufacture_id' => $id_manufacture, 
                                        'vehicle_model_id' => $id_model, 'vehicle_color' => $color,
                                        'created_at' => $created_at, 'approved_at' => $approved_at, 'expired_at' => $expired_at,
                                        'approved' => $approved, 'vehicle_card_status' => $vehicle_card_status, 'meta' => $meta_new]);
            return $user;
        } catch (\Exception $e) { 
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Error! Failed Update User id_user: {$id_user}.  Error:  {$e->getMessage()}";
            Storage::append("error_sync_v2/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }


    public function getAllDataSyncStaff_v2($pageSize, $page) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.NhanVien as nv')
                        ->select('nv.MaNV', 'nv.HoTenNV', 'nv.Mobile', 'nv.Email', 'nv.DiaChi', 'nv.CMND', 'nv.MaCuaHang', 'nv.MaCV', 'nv.MaBoPhan', 'nnv.MaNhomNhanVien')
                        ->join('dbo.NhomNhanVienChiTiet as nnv', 'nnv.MaNhanVien', '=', 'nv.MaNV');
            $query = $result->orderBy('MaNV', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all staff sync table: dbo.NhanVien. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkStaffExist_MaNV($value) {
        try {
            $sql = "meta::jsonb @> '{\"syncData\": [{\"data\": {\"MaNV\":\"$value\"}}]}'::jsonb";
            $result = DB::connection('pgsql')->table('users')
                        ->select('id', 'meta')
                        ->whereRaw($sql)
                        ->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id Staff, MaNV: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function getAllDataStaff_Delete_v2($pageSize, $page) {
        try {
            $result = DB::connection('pgsql')->table('users as us')
                        ->select('us.id','us.meta')
                        ->whereNotNull('us.meta')
                        ->where([['status', '=', EStatus::ACTIVE], ['type', '=', EUser::TYPE_STAFF]]);
            $query = $result->orderBy('us.id', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all staff sync. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_staff_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkExist_delete_MaNV($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.NhanVien as nv')
                ->select('nv.MaNV', 'nv.Mobile')
                ->where('nv.MaNV', '=', $value)->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id Staff, MaNV: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateMetaUser($id_user, $myJSON) {
        try {
            $result = DB::connection('pgsql')->table('users')
                        ->where('id', '=', $id_user)->update(['meta' => $myJSON]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update Meta User, MaNV: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deteleUserById($id_user) {
        try {
            $result = DB::connection('pgsql')->table('users')
                        ->where('id', '=', $id_user)->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete id Staff, id_user: {$id_user}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deteleBranchStaffById($id_user) {
        try {
            $result = DB::connection('pgsql')->table('branch_staff')
                        ->where('user_id', '=', $id_user)->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete id Branch Staff, id_user: {$id_user}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }


    //user

    public function getAllDataUser_Delete_v2($pageSize, $page) {
        try {
            $result = DB::connection('pgsql')->table('users as us')
                        ->select('us.id','us.meta')
                        ->whereNotNull('us.meta')
                        ->where([['status', '=', EStatus::ACTIVE], ['type', '=', EUser::TYPE_USER]]);
            $query = $result->orderBy('us.id', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all user sync. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_user_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkExist_delete_MaKH_KhachHang($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.KhachHang')
                        ->select('MaKH')
                        ->where('MaKH', '=', $value)->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id User dbo.KhachHang, MaKH: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkExist_delete_MaKH_KhachHangNgoai($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.KhachHangNgoai')
                        ->select('MaKH')
                        ->where('MaKH', '=', $value)->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id User dbo.KhachHangNgoai, MaKH: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    //Membership card
    public function getAllDataMemberShip_Card_Delete_v2($pageSize, $page) {
        try {
            $result = DB::connection('pgsql')->table('membership_card')
                        ->select('id', 'user_id', 'meta')
                        ->whereNotNull('meta');
            $query = $result->orderBy('id', 'asc')->forPage($page, $pageSize);
            $total = $result->count();
            $item = $query->get();
            $paginator = new LengthAwarePaginator($item, $total, $pageSize, $page);

            return $paginator;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get all membership card sync. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function checkExist_delete_MaXe($value) {
        try {
            $result = DB::connection('sqlsrv')->table('dbo.XeMuaCHKhac')
                        ->select('MaXe', 'MaKH')
                        ->where('MaXe', '=', $value)->get();
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed get id MemberShipCard dbo.XeMuaCHKhac, MaXe: {$value}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function updateMetaMemberShipCard($id_membership_card, $myJSON) {
        try {
            $result = DB::connection('pgsql')->table('membership_card')
                        ->where('id', '=', $id_membership_card)->update(['meta' => $myJSON]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed update Meta MembershipCard, Id: {$id_membership_card}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }

    public function deteleMemberShipCardById($id_membership_card) {
        try {
            $result = DB::connection('pgsql')->table('membership_card')
                        ->where('id', '=', $id_membership_card)->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            $timestamp = Carbon::now();
            $content_error = "{$timestamp->format('Y:m:d H:i:s')}:  Failed delete MembershipCard, Id: {$id_membership_card}. message: {$e->getMessage()}";
            Storage::append("error_sync_v2/error_membership_card_{$timestamp->format('Y_m_d')}.txt", $content_error); //Error
        }
    }
}