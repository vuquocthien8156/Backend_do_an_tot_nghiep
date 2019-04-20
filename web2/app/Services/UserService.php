<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService {
	private $userRepository;

	public function __construct(UserRepository $userRepository) {
		$this->userRepository = $userRepository;
	}

	public function confirm(string $confirmationCode) {
		return $this->userRepository->confirm($confirmationCode);
	}

    public function getTypeUser($user) {
        return $this->userRepository->getTypeUser($user);
    }
}