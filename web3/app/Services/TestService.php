<?php

namespace App\Services;


use App\Repositorys\TestRepository;

class TestService {
	private $testRepository;

	public function __construct(TestRepository $testRepository) {
		$this->testRepository = $testRepository;
	}

	public function getAllTestData() {
		return $this->testRepository->getAllTestData()->all();
	}
}