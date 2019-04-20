<?php

namespace App\Http\Controllers\Auth;
use Auth;
use App\Constant\SessionKey;
use App\Enums\EUser;
use App\Enums\EStatus;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Services\AuthorizationService;
use App\Traits\CommonTrait;

class LoginController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers;
    use CommonTrait;
	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
    protected $redirectTo = '/customer/manage';
    protected $redirectToAuthorization = '/check-authoziration-user';

	private $userService;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	// public function __construct(UserService $userService) {
	// 	$this->middleware('guest')->except('logout');
	// 	$this->userService = $userService;
	// }
    public function __construct(UserService $userService, AuthorizationService $authorizationService) {
        $this->userService = $userService;
        $this->authorizationService = $authorizationService;
    }

	public function redirectTo() {
		return '/';
	}

	public function username() {
		return 'login_id';
	}

	/**
	 * The user has been authenticated.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  mixed $user
	 * @return mixed
	 */
    protected function authenticated(Request $request, $user) {
        $typeUser = $this->userService->getTypeUser($user);
            if ($typeUser[0]->type === EUser::TYPE_ADMINISTRATOR && $typeUser[0]->status !== EStatus::DELETED) {
                return redirect($this->redirectTo);
            } elseif ($typeUser[0]->type === EUser::TYPE_USER_WEB && $typeUser[0]->status !== EStatus::DELETED) {
                session([SessionKey::AUTHORIZATION_USER => $this->getAuthorizationUser()]);
                return redirect($this->redirectToAuthorization);
            } else {
                auth()->logout();
            }
        return redirect($this->redirectTo);
    }

	protected function credentials(Request $request) {
		$input = $request->only($this->username(), 'password');
		if (!filter_var($input['login_id'], FILTER_VALIDATE_EMAIL)) {
			return [
				'phone' => $input['login_id'],
				'password' => $input['password']
			];
		}
		return [
			'email' => $input['login_id'],
			'password' => $input['password']
		];
    }
}
