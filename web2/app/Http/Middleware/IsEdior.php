<?php

namespace App\Http\Middleware;

use App\Enums\EUserRole;
use Closure;

class IsEdior {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		if (auth() && auth()->user()->hasRole(EUserRole::EDITOR)) {
			return $next($request);
		}
		return redirect('/');
	}
}
