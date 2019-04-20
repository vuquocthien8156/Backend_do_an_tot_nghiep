<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EDateFormat;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Enums\EUserRole;
use App\Enums\ECodePermissionGroup;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use App\Services\CustomerService;
use App\Services\EmployeesService;
use App\Traits\CommonTrait;
use Excel;
use App\Enums\EManufacture;
use App\Services\ConfigService;
use App\Exports\EmployeesExport;
use App\Services\AuthorizationService;
class EmployeesController extends Controller {
	use CommonTrait;

    public function __construct(ConfigService $configService, EmployeesService $employeesService, 
                                CustomerService $customerService, AuthorizationService $authorizationService) {
        $this->configService = $configService;
        $this->employeesService = $employeesService;
        $this->customerService = $customerService;
        $this->authorizationService = $authorizationService;
    }

    public function twoDigitNumber($number) {
		return $number < 10 ? '0'.$number : $number;
    }

    public function viewEmployees() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::STAFF)) {
            return abort(403, 'Unauthorized action!');
        }
        $listBranch = $this->configService->getListBranch();
        $listStaff = $this->employeesService->getStaffType();
        return view('employees.manage-employees', ['listBranch' => $listBranch, 'listStaff' => $listStaff]);
    }

    public function viewAddEmployees() {
        if (Gate::denies('enable_feature', ECodePermissionGroup::STAFF)) {
            return abort(403, 'Unauthorized action!');
        }
        if (Session::has('authorization_user')) {
            $user_id = auth()->id();
            $code_page = ECodePermissionGroup::STAFF;
            $check_authorization = $this->authorizationService->checkAuthorizationPage($user_id, $code_page);
            if(!isset($check_authorization)) {
                return abort(403, 'Unauthorized action!');
            }
        }
        $listBranch = $this->configService->getListBranch();
        $listStaff = $this->employeesService->getStaffType();
        return view('employees.add-new-employees', ['listBranch' => $listBranch,'listStaff' => $listStaff]);
    }

    public function saveEmployees(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::STAFF)) {
            return abort(403, 'Unauthorized action!');
        }
        try {
            $name = $request->get('name_employees');
            $phone = $request->get('phone_employees');
            $email = $request->get('email_employees');
            $branch_id = $request->get('branch');
            $type_employees = $request->get('type_employees');
            $avatar_path = null;
            $created_by = auth()->id();
            $now = Carbon::now();
            if ($request->file('files') != null || $request->file('files') != '') {
                $subName = 'employees/avatar/'.$now->year.$this->twoDigitNumber($now->month).$this->twoDigitNumber($now->day);         
                $destinationPath = config('app.resource_physical_path');
                $pathToResource = config('app.resource_url_path');
                $filename = str_random(30) . '.' . $request->file('files')->getClientOriginalExtension();
                $check = $request->file('files')->move($destinationPath . '/' . $subName, $filename);
                if (!file_exists($check)) {
                    return \Response::json(false);
                }
                $avatar_path = $subName . '/' . $filename;
            }
            $saveEmployees = $this->employeesService->saveEmployees($name, $phone, $email, $avatar_path, $type_employees, $created_by);
            $staff_id = $saveEmployees->id;
            $saveStaffBranch = $this->employeesService->saveStaffBranch($branch_id, $staff_id, $created_by);
            return redirect()->route('employees-view');
        } catch (\Exception $e) {
            logger('Fail Save branch staff, branch_id: ' . $branch_id , ['e' => $e]);
            return null;
        }
    }

    public function doSearchEmployees(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::STAFF)) {
            return abort(403, 'Unauthorized action!');
        }
        if ($request->ajax()) {
            $name_phone_email = $request->get('name_phone_email');
            $branch_id = $request->get('branch_id');
            $status = $request->get('status');
            $type_employees = $request->get('type_employees');
            $infoExportExcel = ['name_phone_email'=>$name_phone_email, 'branch_id'=>$branch_id, 'status'=>$status, 'type_employees' => $type_employees];

            $listSearchEmployees = $this->employeesService->searchEmployees($branch_id, $name_phone_email, $status, $type_employees);
            $timezone = $this->getUserTimezone();
            $pathToResource = config('app.resource_url_path');
			$tmp = $listSearchEmployees->map(function ($item) use ($timezone, $pathToResource) {
				return [
                    'id' => $item->id,
                    'phone' => $item->phone,
                    'name' => $item->name,
                    'email' => $item->email,
                    'avatar_path' => $item->avatar_path,
                    'path_to_resource' => $pathToResource,
                    'branch_name' => $item->branch_name,
					'status' => EUser::valueToName($item->status),
                    'created_at' => isset($item->created_at) ? Carbon::parse($item->created_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT) : null,
                    'category_name' => $item->category_name,
                    'branch_id' => $item->branch_id,
                    'categoryid' => $item->categoryid,
                    'birthday' => $item->birthday,
				];
            });

			$listSearchEmployees->setCollection($tmp);
			return response()->json(['listSearchEmployees'=>$listSearchEmployees,'listEmployeesExport'=>$infoExportExcel]);
		}
		return response()->json([]);
    }

    public function deleteEmployees(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::STAFF)) {
            return abort(403, 'Unauthorized action!');
        }
        $isDelete = $request->get('id_staff');
        $deleteCustomer = $this->customerService->deleteCustomer($isDelete);
        $deleteEmployees = $this->employeesService->deleteEmployees($isDelete);
        $deleteEmployeesUser = $this->employeesService->deleteEmployeesUser($isDelete);
        if (isset($deleteEmployees) && isset($deleteEmployeesUser)) {
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } else {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
    }

    public function doExportExcelEmployees(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::STAFF)) {
            return abort(403, 'Unauthorized action!');
        }
        $name_phone_email = $request->get('name_phone_email');
        $branch_id = $request->get('branch_id');
        $status = $request->get('status');
        $type_employees = $request->get('type_employees');
        return Excel::download(new EmployeesExport($name_phone_email, $branch_id, $status, $type_employees), 'employees-411.xlsx');
    }

    public function updateEmployees(Request $request) {
        if (Gate::denies('enable_feature', ECodePermissionGroup::STAFF)) {
            return abort(403, 'Unauthorized action!');
        }
        try {
            $id_employess = $request->get('id_user_edit');
            $name_edit = $request->get('name_employees_edit');
            $email_edit = $request->get('email_edit');
            $branch_id_edit = $request->get('branch_name_edit');
            $category_id_edit = $request->get('category_name_edit');
            $birthday_edit = $request->get('birthday_edit');
            $avatar_path = $request->get('avatar_path');
            $update_by = auth()->id();
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

            $employees_result = $this->employeesService->updateEmployees($id_employess, $name_edit, $email_edit, $avatar_path, $category_id_edit, $update_by, $now, $birthday_edit);
            
            $branch_result = $this->employeesService->updateBranchStaff($id_employess, $branch_id_edit, $update_by, $now);

            if (isset($employees_result) && isset($employees_result)) {
                return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
            } else {
                return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error !']);
            }
            
        } catch (Exception $e) {
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => $e]);
        }
        
    }
}