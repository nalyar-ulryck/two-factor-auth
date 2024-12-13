<?php

namespace NalyarUlryck\TwoFactorAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // Usuário está logado, mas ainda não configurou o 2FA
        if (Auth::check() && !Auth::user()->google2fa_secret) {
            $request->session()->put('2fa_authenticated', false);
            if (!$request->session()->get('2fa_authenticated')) {
                return redirect()->route('enable2fa');
            }
        }
        // Usuário configurou o 2FA, mas não autenticou
        elseif (Auth::check() && Auth::user()->google2fa_secret) {
            if (!$request->session()->get('2fa_authenticated')) {
                return redirect()->route('verify-2fa');
            }
        }

        return $next($request);
    }
}
