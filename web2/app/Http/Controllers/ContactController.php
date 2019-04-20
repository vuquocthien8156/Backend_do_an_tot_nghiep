<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller {
	private $contactService;

	public function __construct(ContactService $contactService) {
		$this->contactService = $contactService;
	}

	public function viewContact(Request $request) {
		$id = auth()->id();
		if ($id != null) {
			$getInfoUser = $this->contactService->getUserInfo($id);
		} else {
			$getInfoUser = null;
		}
		return view('contact.contact',['getInfoUser' => $getInfoUser]);
	}

	public function postContact(Request $request) {
		if ($request->isMethod('post')) {
			$params = $request->all();
			$name = $request->input('contact_name');
			$email = $request->input('contact_email');
			$phone = $request->input('contact_phone');
			$content = $request->input('contact_content');
			$user_id = auth()->id();
			$saveUserContactMessage = array(
				'user_id' => $user_id,
				'name'	=> $name,
				'email'	=> $email,
				'phone'	=> $phone,
				'message' => $content
			);
            $captchaParams = [
                'secret' => env('GOOGLE_CAPTCHA_SECRET'),
                'response' => $params['g-recaptcha-response']
            ];
            $verifyCaptcha = json_decode($this->verifyCaptcha($captchaParams));
            if (!$verifyCaptcha->success) {
				$getInfoUser = null;
                return view('contact.contact',['getInfoUser' => $getInfoUser])
                    ->withErrors(['Lỗi xác thực']);
			} else {
				$result = $this->contactService->saveUserContactMessage($saveUserContactMessage);
			}
    	}
	}

	public function verifyCaptcha($params) {
		$verifyUrl = env('GOOGLE_CAPTCHA_VERIFY_LINK', 'https://www.google.com/recaptcha/api/siteverify');
		$ch = curl_init($verifyUrl);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
		curl_close ($ch);
		return $result;
	}
}    