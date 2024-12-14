@extends('twofactor::components.layout-2fa-page')
@section('title', '2fa')
@section('content')
    <x-twofactor::card-login>
        @slot('title')
        <p id="title">Autenticação 2 Fatores</p>
            <span class="text">Informe o Código OTP para completar seu login.</span>
        @endslot
        @slot('btnSend')
            <x-twofactor::btn>
                Enviar
            </x-twofactor::btn>
            <script>
                const button = document.getElementById('btn-send');
                document.addEventListener('DOMContentLoaded', function() {
                    const btnSend = document.getElementById('btn-send');
                    let otp = document.getElementById('otp-value');

                    otp.addEventListener('input', () => {
                        maskOtp(otp);
                    });


                    function handleEvent(event) {
                        event.preventDefault();
                        if (event.type === 'click') {
                            if (otp.value) {
                                sendOTP();
                            }

                        } else if (event.key === 'Enter') {
                            if (otp.value) {
                                sendOTP();
                            }
                        }
                    }



                    // Função para enviar a requisição POST
                    async function sendOTP() {
                        const errorMessage = document.getElementById('error-message');

                        try {
                            const response = await fetch("{{ route('verify2fa') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    'otp': otp.value,
                                    'verify': true
                                })
                            });

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.message || 'Erro ao verificar o código OTP.');
                            }
                            button.classList.add('sent');
                            button.innerHTML = '<div class="checkmark"></div>';
                            const data = await response.json();
                            setTimeout(() => {

                                window.location.href = data.redirect;
                            }, 800);
                            // Sucesso: Redireciona ou executa uma ação
                        } catch (error) {

                            errorMessage.textContent = error || 'Erro inesperado. Tente novamente.';
                        }
                    }

                    function maskOtp(otp) {

                        if (otp.value) {

                            const valorSemFormatacao = otp.value.replace(/\D/g, '');

                            const tamanhoMaximo = 6;
                            const valorFormatado = valorSemFormatacao.slice(0, tamanhoMaximo);
                            const valorComMascara = valorFormatado.replace(/(\d{3})(\d+)/, '$1 $2');

                            otp.value = valorComMascara;
                        }

                    }
                    btnSend.addEventListener('click', handleEvent);
                    otp.addEventListener('keyup', handleEvent);

                });
            </script>
        @endslot
    @section('login')
        @if (Route::has('login'))
            @auth
                <a href="{{ url()->previous() }}"
                    class="back-login"
                    style="font-size: 14px;">
                        <span>
                            Retorna
                            para Página de Login
                        </span> <span class="material-symbols-outlined" style="font-size: 20px;transform: translateX(-27%)translatey(30%);">
                            undo
                        </span>
                </a>
            @endauth
        @endif
    @endsection
</x-twofactor::card-login>
@endsection
