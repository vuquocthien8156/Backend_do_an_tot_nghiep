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

    public function listBranch($name, $place) {
        return $this->branchRepository->listBranch($name, $place);
    }

}