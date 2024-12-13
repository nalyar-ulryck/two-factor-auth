@extends('twofactor::components.layout-2fa-page')
@section('title', '2fa')
@section('content')
    <x-twofactor::card-login>
        @slot('title')
            <div class="login-title mt-4">
                <h2 class="text-center text-dark">Habilitar 2FA</h2>
            </div>
            <div class="mb-4 text-justify">
                <p>Escaneie este código QR com o aplicativo Google Authenticator e insira o código para
                    habilitar a autenticação de dois fatores.</p>
                <div class="d-flex justify-content-center my-4">
                    <img src="{!! $QR_Image !!}" alt="" srcset="">
                </div>
            </div>
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
                                    'created': true
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
    </x-twofactor::card-login>
@endsection
