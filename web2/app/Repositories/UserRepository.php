<?php

namespace App\Repositories;

use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserRepository extends BaseRepository {
	public function __construct(User $user) {
		$this->model = $user;
	}

	/**
	 * Confirm a user.
	 *
	 * @param  string $confirmationCode
	 * @return \App\User
	 */
	public function confirm(string $confirmationCode) {
		return DB::transaction(function () use ($confirmationCode) {
			$user = User::where('register_confirm_code', $confirmationCode)->first();
			if ($user) {
				$user->register_confirm_code = null;
				$user->status = EUser::STATUS_ACTIVE;
				$user->save();
				return $user;
			}
			return null;
		});
    }

    public function getTypeUser($user) {
		$type = User::select('type', 'status')->where('id', $user->id)->get();
		return $type;
	}
}