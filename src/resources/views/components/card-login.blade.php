<div class="container">
    {{ $title }}

    @include('twofactor::components.input')

    {{ $btnSend }}

    @yield('btn-back')
</div>
