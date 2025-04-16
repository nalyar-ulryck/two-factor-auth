<?php

namespace NalyarUlryck\TwoFactorAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class TwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // Usuário está logado, mas ainda não configurou o 2FA
        if (!($request->route()->getPrefix() === 'api')) {
            if (Auth::check() && !Auth::user()->google2fa_secret) {
                $request->session()->put('2fa_authenticated', false);
                if (!$request->session()->get('2fa_authenticated')) {
                    return response()->view('twofactor::enable');

                }
            }
            // Usuário configurou o 2FA, mas não autenticou
            elseif (Auth::check() && Auth::user()->google2fa_secret) {
                if (!$request->session()->get('2fa_authenticated')) {
                    return response()->view('twofactor::verify2fa');
                }
            }
        }

        if (Auth::check() && !Auth::user()->google2fa_secret) {
            $request->session()->put('2fa_authenticated', false);
            if (!$request->session()->get('2fa_authenticated')) {
                return response()->json([
                    'mensagem' => '2fa Não Cadastrado'
                ], 404);

            }
        }
        // Usuário configurou o 2FA, mas não autenticou
        elseif (Auth::check() && Auth::user()->google2fa_secret) {
            if (!$request->session()->get('2fa_authenticated')) {
                return response()->json([
                    'mensagem' => '2fa Não autenticao'
                ], 401);
            }
        }

        return $next($request);
    }
}
