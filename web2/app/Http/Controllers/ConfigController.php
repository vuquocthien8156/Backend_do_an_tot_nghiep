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
use App\Enums\EManufacture;
use App\Enums\ECodePermissionGroup;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Services\CustomerService;
use App\Services\ConfigService;
use App\Services\AuthorizationService;

class ConfigController extends Controller {
    private $customerService;

	public function __construct(CustomerService $customerService, ConfigService $configService, AuthorizationService $authorizationService) {
        $this->customerService = $customerService;
        $this->configService = $configService;
        $this->authorizationService = $authorizationService;
	}
    // Manufacture - model
    public function viewConfigManufactureModel() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $listManufacture = $this->customerService->getManufacture();
        return view('config.manufacture-model', ['listManufacture' => $listManufacture]);
    }

    public function getManufactureModel(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        if ($request->ajax()) {
            $id_manufacture = $request->get('id_manufacture');
            $listManufacture = $this->customerService->getManufactureModal($id_manufacture);
			return response()->json($listManufacture);
		}
		return response()->json([]);
    }

    public function saveManufacture(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        
        try {
            //Delete manufacture
            $id_category = $request->input('id_category');
            if ($id_category > 0) {
                $status = EStatus::DELETED;
                $type_category = EManufacture::MANUFACTURE;
                        $updateStatusCategoryById = $this->configService->updateStatusCategoryById($id_category, $status, $type_category);
                
            }
            //Save Manufacture new
            $total_manufacture_new = $request->input('total_manufacture_new');
            if ($total_manufacture_new > 0) {
                for ( $i = 0; $i < $total_manufacture_new; $i++ ) {
                    $manufacture_new = $request->input('input_manufacture_new' . $i);
                    if ( $manufacture_new !== null && $manufacture_new !== '') {
                        list($name_manufacture, $logo_path) = explode("__", $manufacture_new);
                        $saveManufacture = $this->configService->saveManufacture($name_manufacture, $logo_path);
                        
                        $name_model = "DÒNG KHÁC";
                        $logo_path_model = null;
                        $id_manufacture = $saveManufacture->id;
                        $saveModel = $this->configService->saveModel($name_model, $logo_path_model, $id_manufacture);
                    }
                } 
            }

            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } catch (\Exception $e) {
            logger('Fail Save Manufacture . message: ', ['e' => $e]);
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function saveModel(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        try {
            //Delete model
            if ($request->ajax()) {
                $id_category = $request->input('model');
                if(isset($id_category)) {
                    $status = EStatus::DELETED;
                    $type_category = EManufacture::MANUFACTURE_MODEL;
                    $updateStatusCategoryById = $this->configService->updateStatusCategoryById($id_category, $status, $type_category);
                }
            }
            //Save model new
            $total_model_new = $request->input('total_model_new');
            if ($total_model_new > 0) {
                for ( $i = 0; $i < $total_model_new; $i++ ) {
                    $model_new = $request->input('input_model_new' . $i);
                    if ( $model_new !== null && $model_new !== '') {
                        list($id_manufacture, $name_model, $logo_path_model) = explode("__", $model_new);
                        $saveModel = $this->configService->saveModel($name_model, $logo_path_model, $id_manufacture);
                    }
                } 
            }

            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } catch (\Exception $e) {
            logger('Fail Save Manufacture . message: ', ['e' => $e]);
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function sortDisplayOrderManufacture(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        try {
            $data = $request->get('id_manufacture_index');
            $tmp = explode(",", $data);
            $type_category = EManufacture::MANUFACTURE;
            foreach ($tmp as $key => $value) {
                list($id_category, $numerical_order) = explode("_", $value);
                if($id_category != "" || $id_category != null) {
                    $sort = $this->configService->sortDisplayOrder($id_category, $numerical_order, $type_category);
                }
            }
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } catch (\Exception $e) {
            logger('Fail Save Manufacture. message: ', ['e' => $e]);
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function sortDisplayOrderModel(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        try {
            $data = $request->get('id_model_index');
            $tmp = explode(",", $data);
            $type_category = EManufacture::MANUFACTURE_MODEL;
            foreach ($tmp as $key => $value) {
                list($id_category, $numerical_order) = explode("_", $value);
                if($id_category != "" || $id_category != null) {
                    $sort = $this->configService->sortDisplayOrder($id_category, $numerical_order, $type_category);
                }
            }
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } catch (\Exception $e) {
            logger('Fail Save Manufacture . message: ', ['e' => $e]);
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function viewBranch() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        if (Session::has('authorization_user')) {
            $user_id = auth()->id();
            $code_page = ECodePermissionGroup::CONFIG;
            $check_authorization = $this->authorizationService->checkAuthorizationPage($user_id, $code_page);
            if(!isset($check_authorization)) {
                return abort(403, 'Unauthorized action!');
            }
        }
        $listBranch = $this->configService->getBranch();
        return view('config.create-branch', ['listBranch' => $listBranch]);
    }

    public function saveBranch(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $name_branch = $request->get('name_branch');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $address = $request->get('address');
        $phone_branch = $request->get('phone_branch');
        $other_information = $request->get('other_information');

        $saveBranch = $this->configService->saveBranch($name_branch, $latitude, $longitude, $address, $phone_branch, $other_information);
        if (isset($saveBranch)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function deleteBranch(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $id_branch = $request->get('id_branch');
        $deleteBranch = $this->configService->deleteBranch($id_branch);
        if (isset($deleteBranch)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function updateBranch(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $name_branch_update = $request->get('name_branch_update');
        $latitude_update = $request->get('latitude_update');
        $longitude_update = $request->get('longitude_update');
        $address_update = $request->get('address_update');
        $phone_branch_update = $request->get('phone_branch_update');
        $other_infomation_update = $request->get('other_infomation_update');
        $id_branch_update = $request->get('id_branch_update');

        $saveBranch = $this->configService->updateBranch($id_branch_update, $name_branch_update, $latitude_update, $longitude_update, $address_update, $phone_branch_update, $other_infomation_update);
        if (isset($saveBranch)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function getContentBirthday() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $contentBirthday = $this->configService->getContentBirthday();
        return view('config.birthday', ['contentBirthday' => $contentBirthday]);
    }

    public function updateContentBirthday(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $content = $request->get('content_birthday');
        $updateContentBirthday = $this->configService->updateContentBirthday($content);
        if (isset($updateContentBirthday)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function getContentBankTranfer() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $contentBankTranfer = $this->configService->getContentBankTranfer();
        return view('config.bank-tranfer', ['contentBankTranfer' => $contentBankTranfer]);
    }

    public function updateContentBankTranfer(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        $content = $request->get('content_tranfer');
        $updateContentBankTranfer= $this->configService->updateContentBankTranfer($content);
        if (isset($updateContentBankTranfer)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }
}
