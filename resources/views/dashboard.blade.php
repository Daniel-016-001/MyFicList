<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyFicList') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-950 text-gray-100">
    @include('layouts.navigation')

    <header class="bg-gradient-to-r from-gray-900 to-gray-950 border-b border-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-bold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                    Mi Dashboard
                </h2>
                <a href="/" class="text-blue-400 hover:text-blue-300 font-bold transition">
                    <i class="fas fa-search mr-2"></i>Buscar
                </a>
            </div>
        </div>
    </header>

    <main class="bg-gray-950 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="mb-12 bg-gradient-to-r from-blue-900/20 to-purple-900/20 border border-blue-800/30 rounded-xl p-8">
                <h3 class="text-2xl font-bold mb-2">¡Bienvenido, {{ Auth::user()->name }}! 👋</h3>
                <p class="text-gray-400">Organiza tu colección de entretenimiento favorito</p>
            </div>

            <!-- Collection -->
            <h2 class="text-2xl font-bold mb-6">Tu Colección</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @php
                    $userLists = Auth::user()->userLists ?? collect();
                @endphp
                @forelse($userLists as $item)
                    <div class="group bg-gray-900 rounded-lg overflow-hidden border border-gray-800 hover:border-blue-500 transition shadow-lg hover:shadow-2xl">
                        <img src="{{ $item->media->cover_url }}" alt="{{ $item->media->title }}" class="w-full h-64 object-cover">
                        <div class="p-3">
                            <h4 class="font-bold text-sm line-clamp-2 mb-2">{{ $item->media->title }}</h4>
                            <a href="{{ route('media.show', $item->media->id) }}" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded inline-block">Ver</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <p class="text-gray-400 mb-4">Tu lista está vacía</p>
                        <a href="/" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded-lg">Explorar Contenido</a>
                    </div>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>
