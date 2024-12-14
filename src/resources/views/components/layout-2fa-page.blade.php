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
    <!-- CSS -->
    <style>
        body {
            font-family: Inter, sans-serif;
            background-color: #ECF0F4;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100dvh;
            color: #031e23;
            font-weight: ;

        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0px 28px rgba(0, 0, 0, .08);
            padding: 40px;
            max-width: 400px;
            text-align: center;
            position: relative;
        }

        #title {
            font-size: 26px;
            font-weight: 900;
            display: inline;
            margin-bottom: 40px;

            text-align: center;
            color: #333;
            /* margin-bottom: 26px; */
        }



        .qrcode {
            margin: 20px 0;
        }

        .input-group {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
            margin-bottom: 6px;
            max-width: 100%;
            transition: box-shadow 0.3s, border-color 0.3s;
        }

        .input-group input {
            border: none;
            outline: none;
            flex: 1;
            padding: 10px;
            font-size: 18px;
        }

        .input-group input:focus {
            outline: none;
        }


        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24;
        }

        .erro {
            position: absolute;
            color: red;
            font-weight: 600
        }

        .text {
            font-size: 14px;
            margin-top: 30px;
            display: block;
            color: #333;
            margin-left: 2px;
            margin-bottom: 2px;
        }

        .back-login {
            color: #6c757d;
            position: absolute;
            bottom: 14px;
            right: 10px;
        }

        .back-login:hover {
            color: #343a40;
        }

        .back-login:focus {
            border-radius: 0.2rem;
            outline-width: 2px;
            outline-color: #dc3545;
        }

        .btn-position {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .button {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 50px;
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-family: Inter, sans-serif;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: width 0.5s ease, background-color 0.3s ease;
        }

        .button:hover {
            background-color: #3b82f6;
        }

        .button.sent {
            width: 50px;
            border-radius: 1000px;
            background-color: #28A745;
        }

        .checkmark {
            position: absolute;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 0;
            height: 0;
            border-right: 6px solid white;
            border-bottom: 6px solid white;
            transform: rotate(43deg);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .button.sent .checkmark {
            width: 15px;
            height: 15px;
            opacity: 1;
            animation: draw-check 0.5s ease forwards;
        }


        @keyframes draw-check {
            0% {
                width: 0;
                height: 0;
            }

            50% {
                width: 12px;
                height: 0;
            }

            100% {
                width: 12px;
                height: 22px;
            }
        }
    </style>
</head>

<body>
    @yield('content')
</body>

</html>
