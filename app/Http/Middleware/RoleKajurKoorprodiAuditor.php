<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleKajurKoorprodiAuditor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        $user = Auth::user();

        if ($user->role_id == 2 || $user->role_id == 3 || $user->role_id == 4)
            return $next($request);

        return redirect('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
    }
}