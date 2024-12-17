@extends('twofactor::components.layout-2fa-page')
@section('title', '2fa')
@section('content')
    <x-twofactor::card-login>
        @slot('title')
            <h1>Habilitar 2FA</h1>
            <p>Escaneie este código QR com o aplicativo Google Authenticator e insira o código para habilitar a autenticação
                de dois fatores.</p>
            <div class="qrcode">
                <img src="{!! $QR_Image !!}" alt="" srcset="">
            </div>
        @endslot
        @slot('btnSend')
            <x-twofactor::btn-send>
                Enviar
                @slot('sendType')
                    created
                @endslot
            </x-twofactor::btn-send>
        @endslot
    </x-twofactor::card-login>
@endsection
