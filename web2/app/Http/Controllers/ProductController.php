<?php

namespace App\Http\Controllers;

use App\Constant\ConfigKey;
use App\Constant\SessionKey;
use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\ErrorCode;
use App\Helpers\ConfigHelper;
use App\Services\ProductService;;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Excel;

class ProductController extends Controller {
	use CommonTrait;

	private $vehicleService;

	public function __construct(ProductService $productService) {
		$this->productService = $productService;
	}

	public function searchProduct(Request $request) {  
		$result = $this->productService->searchProduct();
		if ($result == true) {
			return \Response::json(['status' =>"ok",'success' => true, 'listProduct' => $result]);
		}
		return \Response::json(['status' =>"error",'success' => false, 'error' => 1]);
	}
}
