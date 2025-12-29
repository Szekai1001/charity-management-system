<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;500;600;700&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .login-bg {
            position: relative;
            background-image: url('/image/signBoard.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* FIX: 'fixed' prevents the background from jumping when mobile keyboard opens */
            background-attachment: fixed; 
            min-height: 100vh;
            width: 100%;
        }

        /* Dark Overlay */
        .login-bg::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Darkens the image */
            z-index: 0; 
        }
    </style>
</head>

<body class="text-gray-900 antialiased font-spartan">
    
    <div class="login-bg flex flex-col justify-center items-center py-6 sm:pt-0">
        
        <div class="relative z-10 w-full px-4 sm:px-0 sm:max-w-md">
            
            <div class="w-full px-6 py-8 bg-white/90 shadow-xl rounded-xl overflow-hidden">
                {{ $slot }}
            </div>
            
        </div>
        
    </div>
</body>

</html>