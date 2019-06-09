<?php

namespace App\Services;

use App\Enums\EStatus;
use App\Enums\EDateFormat;
use App\Repositories\BranchRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Constant\SessionKey;
use Illuminate\Support\Facades\Session;


class BranchService {
    protected $branchRepository;

    public function __construct(BranchRepository $branchRepository) {
        $this->branchRepository = $branchRepository;
    }

    public function listPlace() {
        return $this->branchRepository->listPlace();
    }

    public function listBranch($page) {
        return $this->branchRepository->listBranch($page);
    }

    public function saveBranch($name, $latitude, $longitude, $phone_branch, $address, $id_kv) {
        return $this->branchRepository->saveBranch($name, $latitude, $longitude, $phone_branch, $address, $id_kv);
    }

    public function deleteBranch($id) {
        return $this->branchRepository->deleteBranch($id);
    }

    public function updateBranch($id_branch_update, $address_update, $phone_branch_update, $name_branch_update, $latitude_update, $longitude_update, $id_kv) {
        return $this->branchRepository->updateBranch($id_branch_update, $address_update, $phone_branch_update, $name_branch_update, $latitude_update, $longitude_update, $id_kv);
    }

}