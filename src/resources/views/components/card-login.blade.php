<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
    <div class="container">
        <div class="row align-items-center justify-content-center">

            <div class="col-md-8 col-lg-7">
                <div class="login-box bg-white box-shadow border-radius-10">

                    {{$title}}

                    @include('twofactor::components.input')

                    {{$btnSend}}

                    @yield('login')
                </div>
            </div>
        </div>
    </div>
</div>

