<?php

namespace App\Repositories;


use App\Models\Test;

class TestRepository {
	public function getAllTestData() {
		return Test::all();
	}
}