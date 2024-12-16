<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="{{ asset('/vendor/two-factor-auth/css/app.css') }}" rel="stylesheet">
    <title>@yield('title') - {{ config('app.name') }} </title>


    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>

<body>
    @yield('content')
</body>

</html>
