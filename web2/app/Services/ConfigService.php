<?php

namespace App\Services;

use App\Enums\Banner\EBannerActionType;
use App\Enums\ErrorCode;
use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Models\Banner;
use App\Repositories\BannerRepository;
use App\Repositories\ConfigRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ConfigService {
    protected $configRepository;
    private $bannerRepository;

	public function __construct(ConfigRepository $configRepository, BannerRepository $bannerRepository) {
		$this->configRepository = $configRepository;
		$this->bannerRepository = $bannerRepository;
    }

    public function getListBranch() {
        return $this->configRepository->getListBranch();
    }

    public function infoCustomer() {
        return $this->configRepository->infoCustomer();
    }

    public function saveManufacture($name_manufacture, $logo_path) {
        return $this->configRepository->saveManufacture($name_manufacture, $logo_path);
    }

    public function saveModel($name_model, $logo_path_model, $id_manufacture) {
        return $this->configRepository->saveModel($name_model, $logo_path_model, $id_manufacture);
    }

    public function updateStatusCategoryById($id_category, $status, $type_category) {
        return $this->configRepository->updateStatusCategoryById($id_category, $status, $type_category);
    }

    public function updateStatusCategory($status, $type_category) {
        return $this->configRepository->updateStatusCategory($status, $type_category);
    }

    public function sortDisplayOrder($id_category, $numerical_order, $type_category) {
        return $this->configRepository->sortDisplayOrder($id_category, $numerical_order, $type_category);
    }

    public function updateStatusModelCategory($status, $type_category, $parent_category_id) {
        return $this->configRepository->updateStatusModelCategory($status, $type_category, $parent_category_id);
    }

    //Branch
    public function saveBranch($name_branch, $latitude, $longitude, $address, $phone_branch, $other_information) {
        return $this->configRepository->saveBranch($name_branch, $latitude, $longitude, $address, $phone_branch, $other_information);
    }

    public function updateBranch($id_branch_update, $name_branch_update, $latitude_update, $longitude_update, $address_update, $phone_branch_update, $other_infomation_update) {
        return $this->configRepository->updateBranch($id_branch_update, $name_branch_update, $latitude_update, $longitude_update, $address_update, $phone_branch_update, $other_infomation_update);
    }

    public function getBranch() {
        return $this->configRepository->getBranch();
    }

    public function deleteBranch($id_branch) {
        return $this->configRepository->deleteBranch($id_branch);
    }

    public function saveBanner($banner_id, $banner_file, $original_file, array $data, int $logged_in_user_id) {
		return DB::transaction(function() use ($banner_id, $banner_file, $original_file, $data, $logged_in_user_id) {
			if (empty($banner_id)) {
				$banner_model = new Banner();
				$banner_model->created_by = $logged_in_user_id;
			} else {
				$banner_model = $this->bannerRepository->getById($banner_id);
				if (empty($banner_model)) {
					return ['error' => ErrorCode::SYSTEM_ERROR, 'msg' => 'Dữ liệu không hợp lệ. Vui lòng thử lại sau.'];
				}
				$banner_model->updated_by = $logged_in_user_id;
			}

			$banner_model->status = EStatus::ACTIVE;
			$banner_model->type = $data['bannerType'];
			$banner_model->action_on_click_type = $data['bannerActionType'];
			$banner_model->image_ratio = $this->changePartRatio($data['aspectRatio']);
			if (EBannerActionType::OPEN_WEBSITE == $banner_model->action_on_click_type) {
				$banner_model->action_on_click_target = [
					'url' => filter_var($data['urlToOpen'], FILTER_SANITIZE_URL)
				];
			}
			$banner_model->path_to_resource = $this->saveUploadedBannerToDisk($banner_file);
			if (empty($banner_id) || isset($original_file)) {
				$banner_model->original_resource_path = $this->saveUploadedBannerToDisk($original_file);
			}
			$banner_model->save();

			return ['error' => ErrorCode::NO_ERROR];
		});
	}

	private function saveUploadedBannerToDisk($banner_file) {
		$rootPath = config('app.resource_physical_path');
		$client = Storage::createLocalDriver(['root' => $rootPath]);
		
		$relative_path = $this->getRelativePathToSaveBanner();
		$path = $client->putFile($relative_path, $banner_file);
		return $path;
	}

	private function getRelativePathToSaveBanner() {
		return 'banner/' . \Carbon\Carbon::now()->format('ym');
	}

	public function getAllRunningBanner() {
		$banners = $this->configRepository->getAllRunningBanner();
// 		$grouped_banners = $banners->groupBy('type');
// 		foreach ($grouped_banners as $type => $banners_in_group) {
// //			$sorted_items = $banners_in_group->sortBy('seq')->sortBy('id');
// 			$sorted_items = $banners_in_group;
// 			$grouped_banners->put($type, $sorted_items);
// 		}
		return $banners;
	}

	public function deleteBanner($banner_id, int $logged_in_user_id) {
		$banner = $this->bannerRepository->getById($banner_id);
		if (empty($banner)) {
			return ['error' => ErrorCode::SYSTEM_ERROR, 'msg' => 'Dữ liệu không hợp lệ. Vui lòng thử lại sau.'];
		}

		$banner->status = EStatus::DELETED;
		$banner->deleted_at = \Carbon\Carbon::now();
		$banner->deleted_by = $logged_in_user_id;
		$banner->save();

		return ['error' => ErrorCode::NO_ERROR, 'msg' => 'Xóa banner thành công.'];
	}

	public function saveDisplayOrder($banner_type, $ordered_ids, int $logged_in_user_id) {
		return DB::transaction(function() use ($banner_type, $ordered_ids, $logged_in_user_id) {
			$order = 1;
			foreach ($ordered_ids as $id) {
				$banner = $this->bannerRepository->getById($id);
				if (empty($banner)) {
					return ['error' => ErrorCode::SYSTEM_ERROR, 'msg' => 'Dữ liệu không hợp lệ. Vui lòng thử lại sau.'];
				}

				$banner->seq = $order++;
				$banner->save();
			}
			return ['error' => ErrorCode::NO_ERROR, 'msg' => 'Lưu thứ tự hiển thị thành công.'];
		});
    }
    
    public function sortDisplayOrderBanner($id_banner, $numerical_order, $updated_by) {
        return $this->configRepository->sortDisplayOrderBanner($id_banner, $numerical_order, $updated_by);
    }

    public function getIdTechnicalGroup() {
        return $this->configRepository->getIdTechnicalGroup();
    }

    private function changePartRatio($aspectRatio) {
    	if($aspectRatio > 1.7 && $aspectRatio < 1.78) {
    		return '16:9';
    	} elseif ($aspectRatio > 1.3 && $aspectRatio < 1.34) {
    		return '4:3';
    	} elseif ($aspectRatio == 1) {
    		return '1';
    	} elseif ($aspectRatio == 1.5) {
    		return '3:2';
    	} elseif ($aspectRatio == 0.5625) {
    		return '9:16';
    	} elseif ($aspectRatio == 0.75) {
    		return '3:4';
    	} elseif ($aspectRatio > 0.6 && $aspectRatio < 0.67) {
    		return '2:3';
    	} else {
    		return '3:2';
    	}
    }

    public function getContentBirthday() {
        return $this->configRepository->getContentBirthday();
    }

    public function updateContentBirthday($content) {
        return $this->configRepository->updateContentBirthday($content);
    }

    public function getContentBankTranfer() {
        return $this->configRepository->getContentBankTranfer();
    }

    public function updateContentBankTranfer($content) {
        return $this->configRepository->updateContentBankTranfer($content);
    }
}