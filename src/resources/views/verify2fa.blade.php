@extends('twofactor::components.layout-2fa-page')
@section('title', '2fa')
@section('content')
    <x-twofactor::card-login>
        @slot('title')
            <p id="title">Autenticação 2 Fatores</p>
            <span class="text">Informe o Código OTP para completar seu login.</span>
        @endslot

        @slot('btnSend')
            <x-twofactor::btn-send>
                Enviar
                @slot('sendType')
                    verify
                @endslot
            </x-twofactor::btn-send>
        @endslot

    @section('btn-back')
        @if (Route::has('login'))
            <x-twofactor::btn-back>
                Retornar para Página de Login
            </x-twofactor::btn-back>
        @endif
    @endsection
</x-twofactor::card-login>
@endsection
