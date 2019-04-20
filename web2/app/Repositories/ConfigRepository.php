<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Models\Banner;
use App\Models\Users;
use App\Models\Branch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\Category;
use App\Constant\ConfigKey;

class ConfigRepository {

	public function __construct(Users $users, Branch $branch, Category $category) {
        $this->users = $users;
        $this->branch = $branch;
        $this->category = $category;
    }

    public function getBranchById($id) {
        return $this->branch->find($id);
    }

    public function getListBranch() {
        $result = Branch::select('id', 'name', 'latitude', 'longitude')
                    ->where(['status' => EStatus::ACTIVE])->get();
        return $result;
    }

    public function infoCustomer() {
        $result = DB::table('users')->select('id', 'name', 'phone')
                    ->where(function($query) {
                        $query->where([['status', '=', EStatus::ACTIVE],['type', '=', EUser::TYPE_USER]])
                              ->orWhere([['status', '=', EStatus::ACTIVE], ['type', '=', EUser::TYPE_STAFF]]);
                    })->get();
                    // ->where(['status' => EStatus::ACTIVE, 'type' => EUser::TYPE_USER])
                    // ->orWhere(['status' => EStatus::ACTIVE, 'type' => EUser::TYPE_STAFF])->get();
        return $result;
    }

    public function getIdCustomerByPhone($numberphone_user) {
        $result = DB::table('users')->select('id')
                    ->where(['status' => EStatus::ACTIVE, 'type' => EUser::TYPE_USER, 'phone' => $numberphone_user])->get();
        return $result;
    }

    public function saveManufacture($name_manufacture, $logo_path) {
        try {
            $category = new Category();
            $category->status = EStatus::ACTIVE;
            $category->type = EManufacture::MANUFACTURE;
            $category->name = $name_manufacture;
            $category->logo_path = $logo_path;
            $category->save();
            return $category;
        } catch (\Exception $e) {
            logger('Fail Save category manufacture' . $name_manufacture, ['e' => $e]);
        }
    }

    public function saveModel($name_model, $logo_path_model, $id_manufacture) {
        try {
            $category = new Category();
            $category->status = EStatus::ACTIVE;
            $category->type = EManufacture::MANUFACTURE_MODEL;
            $category->name = $name_model;
            $category->logo_path = $logo_path_model;
            $category->parent_category_id = $id_manufacture;
            $category->save();
            return $category;
        } catch (\Exception $e) {
            logger('Fail Save category model' . $name_model . ' manufacture_id: ' . $id_manufacture, ['e' => $e]);
        }
    }

    public function updateStatusCategory($status, $type_category) {
        try {
            $result = DB::table('category')->where([['type', '=', $type_category]])
                        ->update(['status' => $status]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to update status category. message: " . $e->getMessage());
            return null;
        }
    }

    public function updateStatusModelCategory($status, $type_category, $parent_category_id) {
        try {
            $result = DB::table('category')->where([['type', '=', $type_category], ['parent_category_id', '=', $parent_category_id]])
                        ->update(['status' => $status]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to update status category model. message: " . $e->getMessage());
            return null;
        }
    }
    
    public function updateStatusCategoryById($id_category, $status, $type_category) {
        try {
            $result = DB::table('category')->where([['id', '=', $id_category], ['type', '=', $type_category]])
                        ->update(['status' => $status]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to update status category. message: " . $e->getMessage());
            return null;
        }
    }

    public function sortDisplayOrder($id_category, $numerical_order, $type_category) {
        try {
            $result = DB::table('category')->where([
                                ['id', '=', $id_category], 
                                ['status', '=', EStatus::ACTIVE],
                                ['type', '=', $type_category]])
                        ->update(['seq' => $numerical_order]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to sort display order category. message: " . $e->getMessage());
            return null;
        }
    }

    // Branch 
    public function saveBranch($name_branch, $latitude, $longitude, $address, $phone_branch, $other_information) {
        try {
            $branch = new Branch();
            $branch->status = EStatus::ACTIVE;
            $branch->name = $name_branch;
            $branch->latitude = $latitude;
            $branch->longitude = $longitude;
            $branch->phone1 = $phone_branch;
            $branch->address = $address;
            $branch->description = $other_information;
            $branch->save();
            return $branch;
        } catch (\Exception $e) {
            logger('Fail Save branch ' . $name_branch , ['e' => $e]);
            return null;
        }
    }

    public function updateBranch($id_branch_update, $name_branch_update, $latitude_update, $longitude_update, $address_update, $phone_branch_update, $other_infomation_update) {
        try {
            return DB::transaction(function () use ($id_branch_update, $name_branch_update, $latitude_update, $longitude_update, $address_update, $phone_branch_update, $other_infomation_update){
                $branch = $this->getBranchById($id_branch_update);
                $branch->name = $name_branch_update;
                $branch->latitude = $latitude_update;
                $branch->longitude = $longitude_update;
                $branch->phone1 = $phone_branch_update;
                $branch->address = $address_update;
                $branch->description = $other_infomation_update;
                $branch->save();
                return $branch;
            });
        } catch (\Exception $e) {
            logger('Error update branch', ['e' => $e]);
            return null;
        }
    }

    public function getBranch() {
        $result = DB::table('branch')->select('id', 'name', 'latitude', 'longitude', 'phone1', 'address', 'description')
                    ->where(['status' => EStatus::ACTIVE])->get();
        return $result;
    }

    public function deleteBranch($id_branch) {
        try {
            $result = DB::table('branch')->where([['id', '=', $id_branch]])
                        ->update(['status' => EStatus::DELETED]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to delete branch. message: " . $e->getMessage());
            return null;
        }
    }

	public function getAllRunningBanner() {
		return Banner::where('status', EStatus::ACTIVE)
			->orderBy('type')
			->orderByRaw('seq nulls last')
			->orderBy('id')
			->get();
    }
    
    public function sortDisplayOrderBanner($id_banner, $numerical_order, $updated_by) {
        try {
            $now = Carbon::now();
            $result = DB::table('banner')->where([
                                ['id', '=', $id_banner], 
                                ['status', '=', EStatus::ACTIVE]])
                        ->update(['seq' => $numerical_order]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed to sort display order category. message: " . $e->getMessage());
            return null;
        }
    }

    public function getIdTechnicalGroup() {
        try {
            $now = Carbon::now();
            $result = DB::table('category')->where([
                                ['type', '=', EUser::TYPE_STAFF_SYNC], 
                                ['value', '=', 1]])
                        ->get();
            return $result;
        } catch (\Exception $e) {
            logger("Failed get id Technical group. message: " . $e->getMessage());
            return null;
        }
    }

    public function getContentBirthday() {
        try {
            $result = DB::table('app_config')
                    ->select('text_value')
                    ->where('name', '=', ConfigKey::BIRTHDAY_CUSTOMER)
                    ->get();            
            return $result;
		} catch (\Exception $e) {
			logger("Failed to Get content birth day message: " . $e->getMessage());
			return null;
		}
    }

    public function updateContentBirthday($content) {
        try {
            $now = Carbon::now();
            $result = DB::table('app_config')
                        ->where('name', '=', ConfigKey::BIRTHDAY_CUSTOMER)
                        ->update(['text_value' => $content]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed update content birth day. message: " . $e->getMessage());
            return null;
        }
    }

    public function getContentBankTranfer() {
        try {
            $result = DB::table('app_config')
                    ->select('text_value')
                    ->where('name', '=', ConfigKey::BANK_TRANFER)
                    ->get();            
            return $result;
		} catch (\Exception $e) {
			logger("Failed to Get content bank tranfer message: " . $e->getMessage());
			return null;
		}
    }

    public function updateContentBankTranfer($content) {
        try {
            $now = Carbon::now();
            $result = DB::table('app_config')
                        ->where('name', '=', ConfigKey::BANK_TRANFER)
                        ->update(['text_value' => $content]);
            return $result;
        } catch (\Exception $e) {
            logger("Failed update content bank tranfer. message: " . $e->getMessage());
            return null;
        }
    }
}