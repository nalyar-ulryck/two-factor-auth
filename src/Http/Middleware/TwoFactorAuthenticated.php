<?php

namespace NalyarUlryck\TwoFactorAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use NalyarUlryck\TwoFactorAuth\Http\Controllers\TwoFactorController;

class TwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();


        // Verifica se é uma rota de API
        $isApiRoute = preg_match('/\bapi\b/', $request->route()->getPrefix());
        // dd(!$isApiRoute && $request->hasSession());
        // Lógica para rotas WEB
        if (!$isApiRoute) {

            return response()->json([
                'mensagem' => '2FA não autenticado',
                'codigo' => $request->route()->getPrefix()
            ]);

            // Usuário não configurou 2FA ainda
            if (!$user->google2fa_secret) {
                $request->session()->put('2fa_authenticated', false);
                return response()->view('twofactor::enable');
            }

            // Usuário tem 2FA mas não está autenticado
            if ($user->google2fa_secret && !$request->session()->get('2fa_authenticated')) {
                return response()->view('twofactor::verify2fa');
            }
        }

        // Lógica para rotas API
        else if ($isApiRoute) {
            // Usuário sem 2FA configurado
            if (!$user->google2fa_secret) {
                return response()->json([
                    'mensagem' => '2FA não cadastrado',
                    'codigo' => 'two_factor_not_enabled'
                ], 401);
            }

            // Usuário com 2FA configurado - verificar autenticação
            if ($user->google2fa_secret) {
                // Token específico para 2FA no cabeçalho
                $twoFactorToken = $request->header('X-2FA-Token');
                // Verificar se o token é válido
                $isTokenValid = $twoFactorToken && TwoFactorController::validate2FAToken($twoFactorToken, $user->id);
                // dd($isTokenValid);
                // OU usar cache com chave baseada no user_id
                $twoFactorAuthenticated = Cache::get('2fa_authenticated_' . $user->id, false);

                if (!$isTokenValid || !$twoFactorAuthenticated) {
                    return response()->json([
                        'mensagem' => '2FA não autenticado',
                        'codigo' => 'two_factor_required'
                    ], 401);
                }
            }
        }

        return $next($request);
    }
}
