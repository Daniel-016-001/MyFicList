<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title')@else {{ config('app.name', 'MyFicList') }} @endif</title>
        <link rel="icon" type="image/svg+xml" href="https://myficlist-bucket.s3.eu-north-1.amazonaws.com/favicon.svg">
        <link rel="alternate icon" href="/favicon.ico">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,600,800&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            html { overflow-y: scroll; }
            body { font-family: 'Outfit', sans-serif !important; }
            .site-logo {
                font-family: 'Outfit', sans-serif !important;
                font-size: 24px !important;
                font-weight: 900 !important;
                line-height: 1 !important;
                letter-spacing: -0.05em !important;
                background: linear-gradient(to right, #3b82f6, #a855f7, #ec4899) !important;
                -webkit-background-clip: text !important;
                -webkit-text-fill-color: transparent !important;
                display: inline-block !important;
                user-select: none !important;
                transition: none !important;
                transform: none !important;
            }
        </style>
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-950 text-gray-100">
        <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
        @stack('scripts')
    </body>
</html>
