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

    public function deleteDiscount($id) {
        return $this->productRepository->deleteDiscount($id);
    }

    public function deleteNews($id) {
        return $this->productRepository->deleteNews($id);
    }

    public function loaisp() {
        return $this->productRepository->loaisp();
    }

    public function sanPham() {
        return $this->productRepository->sanPham();
    }

    public function addProduct($avatar_path, $ten, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta) {
        return $this->productRepository->addProduct($avatar_path, $ten, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);
    }

    public function addNews($ten, $ND, $ngay_tao, $avatar_path, $NĐ) {
        return $this->productRepository->addNews($ten, $ND, $ngay_tao, $avatar_path, $NĐ);
    }

    public function addDiscount($avatar_path,$now,$type,$ma, $ten,$MT, $SPT, $ST, $SSPQD, $STQDTT, $NBD ,$NKT, $GHSC,$SSPTK,$SP ) {
        return $this->productRepository->addDiscount($avatar_path,$now,$type,$ma, $ten,$MT, $SPT, $ST, $SSPQD, $STQDTT, $NBD ,$NKT, $GHSC,$SSPTK,$SP );
    }

    public function editProduct($avatar_path, $ten, $id, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta) {
        return $this->productRepository->editProduct($avatar_path, $ten, $id, $ma, $gia_goc, $gia_size_vua, $gia_size_lon, $loaisp, $ngay_ra_mat, $mo_ta);
    }

    public function editDiscount($ten_khuyen_mai, $id,$ma_code,$mo_ta,$so_phan_tram ,$so_tien ,$so_sp_qui_dinh ,$so_tien_qui_dinh_toi_thieu,$gioi_han_so_code ,$ngay_bat_dau ,$ngay_ket_thuc ,$id_now,$type, $so_sp_tang_kem, $avatar_path) {
        return $this->productRepository->editDiscount($ten_khuyen_mai, $id,$ma_code,$mo_ta,$so_phan_tram ,$so_tien ,$so_sp_qui_dinh ,$so_tien_qui_dinh_toi_thieu,$gioi_han_so_code ,$ngay_bat_dau ,$ngay_ket_thuc ,$id_now,$type, $so_sp_tang_kem, $avatar_path);
    }

    public function editNews($avatar_path, $ten, $id, $ND, $date) {
        return $this->productRepository->editNews($avatar_path, $ten, $id, $ND, $date);
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

    public function forTK($dayStart, $dayEnd) {
        return $this->productRepository->forTK($dayStart, $dayEnd);
    }

    public function getIdMax() {
        return $this->productRepository->getIdMax();
    }

    public function getIdMaxNews() {
        return $this->productRepository->getIdMaxNews();
    }

    public function getIdMaxDiscount() {
        return $this->productRepository->getIdMaxDiscount();
    }

    public function inserImage($avatar_path, $getIdMax) {
        return $this->productRepository->inserImage($avatar_path, $getIdMax);   
    }

    public function inserImageDiscount($avatar_path, $getIdMax) {
        return $this->productRepository->inserImageDiscount($avatar_path, $getIdMax);   
    }

    public function inserImageNews($avatar_path, $getIdMax) {
        return $this->productRepository->inserImageNews($avatar_path, $getIdMax);   
    }

    public function searchDiscount($type) {
        return $this->productRepository->searchDiscount($type);
    }

    public function searchNews1($name) {
        return $this->productRepository->searchNews1($name);
    }

    public function getImg($ma_sp, $type) {
        return $this->productRepository->getImg($ma_sp, $type);
    }

    public function deleteImg($ma_sp, $type) {
        return $this->productRepository->deleteImg($ma_sp, $type);
    }
}