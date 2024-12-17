@auth
    <a href="#" id="btn-logout" class="back-login" style="font-size: 14px;">
        <span>
            {{$slot}}
        </span> <span class="material-symbols-outlined" style="font-size: 20px;transform: translateX(-27%)translatey(30%);">
            undo
        </span>
    </a>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnLogout = document.getElementById('btn-logout');

            function handleEvent(event) {
                event.preventDefault();
                if (event.type === 'click') {
                    send();
                    event.disable = true;
                }
            }

            async function send() {
                try {
                    const response = await fetch("{{ route('back-login') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({})
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Sucesso: Redireciona ou executa uma ação
                        window.location.href = data.redirect;
                    }

                } catch (error) {

                }
            }

            btnLogout.addEventListener('click', handleEvent);

        });
    </script>
@endauth
