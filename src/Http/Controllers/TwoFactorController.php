<?php

namespace NalyarUlryck\TwoFactorAuth\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class TwoFactorController extends Controller
{

    public function enable2Fa()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'mensagem' => 'Usuário não autenticado.'
            ], 401);
        }
        Cache::forget('2fa_secret_key_' . $user->id);
        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();
        session(['2fa_secret' => $secretKey]);
        $this->saveSecretKeyToCache($secretKey, $user->id);
        $qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->username ?? $user->email,
            $secretKey
        );
        return response()->json(['QR_Image' => $qrCode]);
    }

    public function verify2Fa(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'created' => 'nullable|boolean',
            'verify' => 'nullable|boolean'
        ]);

        $google2fa = new Google2FA();
        $authId = auth()->id();
        $user = User::find($authId);
        $isApiRequest = preg_match('/\bapi\b/', $request->route()->getPrefix());

        //Limpa o cache
        $this->clear2FAToken($authId);

        // Para criação de novo 2FA
        if ($request->created) {

            $secretKey = $isApiRequest ? Cache::get('2fa_secret_key_' . $user->id, false) : session('2fa_secret');

            if ((isset($secretKey) && $google2fa->verifyKey($secretKey, preg_replace('/\s+/', '', $request->otp)))) {
                $user->google2fa_secret = $secretKey;
                $user->save();

                // Tratar diferentemente para API e Web
                if ($isApiRequest) {
                    // Gerar token 2FA para API
                    $token = $this->generate2FAToken($user->id);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'OTP verificado com sucesso!',
                        'two_factor_token' => $token
                    ], 200);
                } else {
                    // Para web, continua usando sessão
                    $request->session()->put('2fa_authenticated', true);
                    $request->session()->forget('2fa_secret');

                    return response()->json([
                        'status' => 'success',
                        'message' => 'OTP verificado com sucesso!',
                        'redirect' => route(config('twofactor.routes.redirect_after_verify2fa'))
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'O código está incorreto.'
                ], 422);
            }
        }
        // Para verificação de 2FA existente
        elseif ($request->verify) {

            if ($google2fa->verifyKey($user->google2fa_secret, preg_replace('/\s+/', '', $request->otp))) {
                // Tratar diferentemente para API e Web
                if ($isApiRequest) {
                    // Gerar token 2FA para API
                    $token = $this->generate2FAToken($user->id);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'OTP verificado com sucesso!',
                        'two_factor_token' => $token
                    ], 200);
                } else {
                    // Para web, continua usando sessão
                    $request->session()->put('2fa_authenticated', true);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'OTP verificado com sucesso!',
                        'redirect' => route(config('twofactor.routes.redirect_after_verify2fa'))
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'O código está incorreto.'
                ], 403);
            }
        }
    }


    /**
     * Gera e armazena uma chave secreta de 2FA no cache
     *
     * @param int $userId
     * @return string $secretKey
     */
    private function saveSecretKeyToCache($secretKey,$userId)
    {

        // Armazena a chave no cache com expiração em 5 minutos
        Cache::put('2fa_secret_key_' . $userId, $secretKey, now()->addMinutes(5));

        return $secretKey;
    }



    /**
     * Gera um token 2FA para uso em APIs
     *
     * @param int $userId
     * @return string
     */
    private function generate2FAToken($userId)
    {
        // Gerar um token aleatório
        $token = bin2hex(random_bytes(64));

        // Armazena o token 2FA no cache com expiração em 12 minutos
        Cache::put('2fa_token_' . $userId, $token, now()->addHours(12));

        // Também armazena o status autenticado no cache com expiração em 5 minutos
        Cache::put('2fa_authenticated_' . $userId, true, now()->addHours(12));


        return $token;
    }

    /**
     * Limpa os caches gerados para o usuário no 2FA
     *
     * @param int $userId
     * @return void
     */
    public static function clear2FAToken($userId)
    {
        // Remove o cache do token 2FA baseado no usuário
        Cache::forget('2fa_token_' . $userId);

        // Remove o cache do status autenticado do 2FA
        Cache::forget('2fa_authenticated_' . $userId);
    }

    /**
     * Verifica se o token 2FA é válido
     * Para ser usado no middleware
     *
     * @param string $token
     * @param int $userId
     * @return bool
     */
    public static function validate2FAToken($token, $userId)
    {

        // Verificar se o token existe e corresponde ao usuário
        $cachedToken = Cache::get('2fa_token_' . $userId);
        if (isset($cachedToken)) {
            return hash_equals($cachedToken, $token);
        }
        return false;
    }

    public function showVerify2Fa()
    {
        return view('twofactor::verify2fa');
    }

    public function backLogin(Request $request)
    {
        // dd($request);
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'logout com sucesso!',
            'redirect' => route('login')
        ], 200);
    }
}
