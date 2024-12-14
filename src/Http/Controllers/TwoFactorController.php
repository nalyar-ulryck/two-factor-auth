<?php

namespace NalyarUlryck\TwoFactorAuth\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorController extends Controller
{
    protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function enable2Fa()
    {

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login'); // Redireciona se o usuário não estiver autenticado
        }

        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();
        session(['2fa_secret' => $secretKey]);

        $qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->username ?? $user->email,
            $secretKey
        );

        return view('twofactor::enable', ['QR_Image' => $qrCode, 'secret' => $secretKey]);
    }

    public function verify2Fa(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'created' => 'nullable|boolean'
        ]);
        $google2fa = new Google2FA();
        $authId = auth()->id();
        $user = User::find($authId);
        $secretKey = session('2fa_secret');

        if ($request->created) {
            if ((isset($secretKey) && $google2fa->verifyKey($secretKey, preg_replace('/\s+/', '', $request->otp)))) {
                $user->google2fa_secret = $secretKey;
                $user->save();
                $request->session()->put('2fa_authenticated', true);
                $request->session()->forget('2fa_secret');
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verificado com sucesso!',
                    'redirect' => route(config('twofactor.routes.redirect_after_verify2fa'))
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'O código está incorreto.'
                ], 422);
            }
        } elseif ($request->verify) {
            $request->session()->forget('2fa_secret');
            if ($google2fa->verifyKey($user->google2fa_secret, preg_replace('/\s+/', '', $request->otp))) {
                $request->session()->put('2fa_authenticated', true);
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verificado com sucesso!',
                    'redirect' => route(config('twofactor.routes.redirect_after_verify2fa'))
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'O código está incorreto.'
                ], 422);
            }
        }
    }

    public function showVerify2Fa()
    {
        return view('twofactor::verify2fa');
    }
}
