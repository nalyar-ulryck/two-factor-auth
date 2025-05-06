@extends('twofactor::components.layout-2fa-page')
@section('title', '2fa')
@section('content')
    <x-twofactor::card-login>
        @slot('title')
            <h1>Habilitar 2FA</h1>
            <p>Escaneie este código QR com o aplicativo Google Authenticator e insira o código para habilitar a autenticação
                de dois fatores.</p>
            <div class="qrcode">
                <img src="" class="img">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrCode = document.querySelector('.qrcode img');
            (async function() {
                try {
                    const response = await fetch("{{ route('enable2fa') }}", {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) {
                        throw new Error(`Erro na requisição: ${response.status}`);
                    }
                    const data = await response.json();
                    qrCode.src = data.QR_Image;
                } catch (error) {
                    console.error('Erro ao buscar os dados:', error);
                }
            })();
        });
    </script>
@endsection
