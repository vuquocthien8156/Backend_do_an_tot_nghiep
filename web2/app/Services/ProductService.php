<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\ProductRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class ProductService {
    protected $productRepository;

    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }

    public function searchProduct($name, $page, $ma_loai, $mo_ta, $masp) {
        return $this->productRepository->searchProduct($name, $page, $ma_loai, $mo_ta, $masp);
    }

    public function searchProductTK($masp) {
        return $this->productRepository->searchProductTK($masp);
    }

    public function searchProductAPI($name, $page, $ma_loai, $mo_ta , $ma_loai_chinh) {
        return $this->productRepository->searchProductAPI($name, $page, $ma_loai, $mo_ta , $ma_loai_chinh);
    }

    public function searchRankProduct() {
        return $this->productRepository->searchRankProduct();
    }

    public function delete($id) {
		return $this->productRepository->delete($id);
	}

    public function loaisp() {
        return $this->productRepository->loaisp();
    }

    public function addProduct($avatar_path, $ten, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta) {
        return $this->productRepository->addProduct($avatar_path, $ten, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);
    }

    public function editProduct($avatar_path, $ten, $id, $so_lan_order, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta) {
        return $this->productRepository->editProduct($avatar_path, $ten, $id, $so_lan_order, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);
    }

    public function getIdSp() {
        return $this->productRepository->getIdSp();
    }

    public function getAmount($id) {
        return $this->productRepository->getAmount($id);
    }

    public function getlist($id) {
        return $this->productRepository->getlist($id);
    }

    public function searchProductById($id) {
        return $this->productRepository->searchProductById($id);
    }

    public function searchNews($id) {
        return $this->productRepository->searchNews($id);
    }

    public function searchKM($id) {
        return $this->productRepository->searchKM($id);
    }

    public function countProduct($id) {
        return $this->productRepository->countProduct($id);
    }

    public function forWeek($dayStart, $dayEnd) {
        return $this->productRepository->forWeek($dayStart, $dayEnd);
    }

    public function getIdMax() {
        return $this->productRepository->getIdMax();
    }

    public function inserImage($avatar_path, $getIdMax) {
        return $this->productRepository->inserImage($avatar_path, $getIdMax);   
    }

}