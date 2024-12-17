<div class="btn-position">
    <button class="button" id="btn-send">{{ $slot }}</button>
</div>
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
                    button.disabled = true;
                    sendOTP();
                }

            } else if (event.key === 'Enter') {
                if (otp.value) {
                    otp.disabled = true;
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
                        {{$sendType}}: true
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    button.disabled = errorData ? false : true;
                    otp.disabled = errorData ? false : true;
                    otp.value = '';
                    otp.focus();
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
