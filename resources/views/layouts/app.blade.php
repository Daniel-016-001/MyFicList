<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@hasSection('title')@yield('title')@else MyFicList @endif</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

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
    </head>
    <body class="font-sans antialiased bg-gray-950 text-gray-100">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gradient-to-r from-gray-900 to-gray-950 shadow-lg">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow">
                @yield('content')
            </main>

            @include('layouts.footer')
        </div>
    </body>
</html>
