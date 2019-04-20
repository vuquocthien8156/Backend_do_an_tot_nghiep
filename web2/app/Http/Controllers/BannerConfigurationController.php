<?php

namespace App\Http\Controllers;

use App\Enums\Banner\EBannerActionType;
use App\Enums\Banner\EBannerType;
use App\Enums\ECodePermissionGroup;
use App\Enums\ErrorCode;
use App\Services\ConfigService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class BannerConfigurationController extends Controller {
	use CommonTrait;

	private $configService;

	public function __construct(ConfigService $configService) {
        $this->configService = $configService;
    }

    public function index() {
		if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
			return abort(403, 'Unauthorized action!');
		}
		$banners = $this->configService->getAllRunningBanner();
		$static_host = config('app.resource_url_path');
		//foreach ($banners as $type => $banners_in_group) {
			$banners = $banners->map(function ($banner) use ($static_host) {
				$action_on_click_target = null;
				switch ($banner->action_on_click_type) {
					case EBannerActionType::OPEN_WEBSITE:
						$action_on_click_target = $banner->action_on_click_target->url;
				}
				return [
					'id' => $banner->id,
                    'seq' => $banner->seq,
                    'type' => EBannerType::valueToName($banner->type),
                    'type_origin' => $banner->type,
					'pathToResource' => "$static_host/$banner->path_to_resource",
					'originalResource' => "$static_host/$banner->original_resource_path",
					'actionOnClick' => EBannerActionType::valueToName($banner->action_on_click_type),
					'bannerActionType' => $banner->action_on_click_type,
					'actionOnClickTarget' => $action_on_click_target
				];
			})->values();
        //}
        //return view('config.banner.manage-banner', compact('banner_types', 'banners'));
        return view('config.banner.manage-banner', compact('banners'));
	}

    public function store(Request $request) {
		return $this->update($request, null);
    }

    public function update(Request $request, $banner_id) {
		$banner_type = $request->input('bannerType');
		$banner_action_type = $request->input('bannerActionType');
		$aspect_Ratio = $request->input('aspectRatio');
		if (!EBannerType::isValid($banner_type)
			|| !EBannerActionType::isValid($banner_action_type)) {
			return response()->json([
				'error' => ErrorCode::SYSTEM_ERROR,
				'msg' => 'Dữ liệu không hợp lệ. Vui lòng tải lại trang và thử lại.'
			]);
		}

		if (EBannerActionType::OPEN_WEBSITE == $banner_action_type) {
			$validator = Validator::make($request->only('urlToOpen'), [
				'urlToOpen' => 'required|url',
			], [
				'urlToOpen.required' => 'Vui lòng nhập đường dẫn trang web hợp lệ cần mở khi bấm vào hình banner.',
				'urlToOpen.url' => 'Vui lòng nhập đường dẫn trang web hợp lệ.'
			]);

			if ($validator->fails()) {
				return response()->json([
					'error' => ErrorCode::SYSTEM_ERROR,
					'msg' => $validator->errors()->first('urlToOpen')
				]);
			}
		}

		if (!$request->hasFile('banner')
			|| (empty($banner_id) && !$request->hasFile('file'))) {
			return response()->json([
				'error' => ErrorCode::SYSTEM_ERROR,
				'msg' => 'Vui lòng chọn hình ảnh banner'
			]);
		}
		$banner_file = $request->file('banner');
		$original_file = $request->file('file');
		if (!$banner_file->isValid()
			|| (empty($banner_id) && !$original_file->isValid())) {
			return response()->json([
				'error' => ErrorCode::SYSTEM_ERROR,
				'msg' => 'Có lỗi trong quá trình lưu hình ảnh. Vui lòng thử lại sau hoặc liên hệ bộ phận hỗ trợ.'
			]);
		}

		try {
			$result = $this->configService->saveBanner($banner_id, $banner_file, $original_file, $request->only('aspectRatio', 'bannerType', 'bannerActionType', 'urlToOpen'), auth()->id());
			return response()->json($result);
		} catch (\Exception $e) {
			logger()->error('save banner failed', compact('e'));
			return response()->json(['error' => ErrorCode::SYSTEM_ERROR]);
		}
	}

	public function destroy($banner_id) {
		try {
			$result = $this->configService->deleteBanner($banner_id, auth()->id());
			return response()->json($result);
		} catch (\Exception $e) {
			logger()->error('delete banner failed', compact('e'));
			return response()->json(['error' => ErrorCode::SYSTEM_ERROR, 'msg' => 'Có lỗi trong quá trình xử lý. Vui lòng thử lại sau hoặc liên hệ bộ phận hỗ trợ.']);
		}
	}

	// public function saveBannerDisplayOrder(Request $request, int $banner_type) {
	// 	$ordered_ids = $request->orders;
	// 	if (empty($ordered_ids)) {
	// 		return response()->json(['error' => ErrorCode::NO_ERROR, 'msg' => 'Lưu thứ tự hiển thị thành công']);
	// 	}

	// 	if (!EBannerType::isValid($banner_type)) {
	// 		return response()->json([
	// 			'error' => ErrorCode::SYSTEM_ERROR,
	// 			'msg' => 'Dữ liệu không hợp lệ. Vui lòng tải lại trang và thử lại.'
	// 		]);
	// 	}
	// 	try {
	// 		$result = $this->configService->saveDisplayOrder($banner_type, $ordered_ids, auth()->id());
	// 		return response()->json($result);
	// 	} catch (\Exception $e) {
	// 		logger()->error('delete banner failed', compact('e'));
	// 		return response()->json(['error' => ErrorCode::SYSTEM_ERROR, 'msg' => 'Có lỗi trong quá trình xử lý. Vui lòng thử lại sau hoặc liên hệ bộ phận hỗ trợ.']);
	// 	}
    // }

    public function saveBannerDisplayOrder(Request $request) {
		if (Gate::denies('enable_feature', ECodePermissionGroup::CONFIG)) {
            return abort(403, 'Unauthorized action!');
        }
        try {
            $data = $request->get('id_banner_index');
            $tmp = explode(",", $data);
            $updated_by = auth()->id();
            foreach ($tmp as $key => $value) {
                list($id_banner, $numerical_order) = explode("_", $value);
                if($id_banner != "" || $id_banner != null) {
                    $sort = $this->configService->sortDisplayOrderBanner($id_banner, $numerical_order, $updated_by);
                }
            }
            return \Response::json(['error' => ErrorCode::NO_ERROR, 'message' => 'Success!']);
        } catch (\Exception $e) {
            logger('Fail to sort display order . message: ', ['e' => $e]);
            return \Response::json(['error' => ErrorCode::SYSTEM_ERROR, 'message' => 'Error!']);
        }
	}
}