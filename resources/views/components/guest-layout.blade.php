<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyFicList') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,600,800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .hero-gradient {
            background: linear-gradient(135deg, rgba(59,130,246,0.08) 0%, rgba(139,92,246,0.12) 50%, rgba(236,72,153,0.06) 100%);
        }
        .glass {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(20px);
        }
        .input-field {
            background: rgba(31, 41, 55, 0.8);
            color: white;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-field:focus {
            outline: none;
            border-color: rgba(139, 92, 246, 0.6);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.12);
        }
        .input-field::placeholder { color: rgba(107, 114, 128, 0.8); }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-gray-950/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                MyFicList
            </a>
        </div>
    </nav>

    <!-- Main -->
    <main class="flex-grow hero-gradient flex items-center justify-center p-4 py-16">
        {{ $slot }}
    </main>

    <!-- Footer minimal -->
    <footer class="py-4 text-center text-xs text-gray-600">
        © {{ date('Y') }} MyFicList. Tu biblioteca universal de entretenimiento.
    </footer>
</body>
</html>
