<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/deleteAccount',
        'api/login-by-phone',
        'api/login',
        'api/register',
        'dangnhapsdt',
        'api/listcAcount',
        'api/insert-no-mail',
        'api/update-id_fb',
        'api/update-email',
        'api/updateInfo',
        'api/login-fb',
    ];
}
