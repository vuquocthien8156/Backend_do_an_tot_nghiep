<?php

namespace App\Repositories;

use App\Enums\EStatus;
use App\Enums\EUser;
use App\Enums\EManufacture;
use App\Models\Users;
use App\Models\Branch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class ChangePassWordRepository {

	public function __construct(Users $users) {
        $this->users = $users;
    }

    public function changepassword($id_user, $password, $name) {
        try {
            $result = DB::table('users')->where('id', '=', $id_user)->update(['password' => Hash::make($password), 'name' => $name]);
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }
}