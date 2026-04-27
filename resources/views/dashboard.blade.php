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
                    Explorar
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
                <h3 class="text-2xl font-bold mb-2">Bienvenido, {{ Auth::user()->name }}</h3>
                <p class="text-gray-400">Contenido mejor valorado por la comunidad</p>
            </div>

            <!-- Anime -->
            @if(isset($popularByCategory['anime']) && $popularByCategory['anime']->count() > 0)
                <section class="mb-12">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-800 pb-2">Anime</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($popularByCategory['anime'] as $index => $media)
                            <a href="{{ route('media.show', $media->id) }}" class="bg-gray-900 rounded-lg overflow-hidden border border-gray-800 hover:border-blue-500 transition">
                                <img src="{{ $media->cover_url }}" alt="{{ $media->title }}" class="w-full h-56 object-cover">
                                <div class="p-2">
                                    <h4 class="font-bold text-sm line-clamp-2">{{ $media->title }}</h4>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-gray-500">{{ $media->user_ratings_count }}</span>
                                        @if($media->average_score)
                                            <span class="text-xs font-bold text-yellow-500">{{ number_format($media->average_score, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Manga -->
            @if(isset($popularByCategory['manga']) && $popularByCategory['manga']->count() > 0)
                <section class="mb-12">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-800 pb-2">Manga</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($popularByCategory['manga'] as $media)
                            <a href="{{ route('media.show', $media->id) }}" class="bg-gray-900 rounded-lg overflow-hidden border border-gray-800 hover:border-blue-500 transition">
                                <img src="{{ $media->cover_url }}" alt="{{ $media->title }}" class="w-full h-56 object-cover">
                                <div class="p-2">
                                    <h4 class="font-bold text-sm line-clamp-2">{{ $media->title }}</h4>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-gray-500">{{ $media->user_ratings_count }}</span>
                                        @if($media->average_score)
                                            <span class="text-xs font-bold text-yellow-500">{{ number_format($media->average_score, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Películas -->
            @if(isset($popularByCategory['movie']) && $popularByCategory['movie']->count() > 0)
                <section class="mb-12">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-800 pb-2">Películas</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($popularByCategory['movie'] as $media)
                            <a href="{{ route('media.show', $media->id) }}" class="bg-gray-900 rounded-lg overflow-hidden border border-gray-800 hover:border-blue-500 transition">
                                <img src="{{ $media->cover_url }}" alt="{{ $media->title }}" class="w-full h-56 object-cover">
                                <div class="p-2">
                                    <h4 class="font-bold text-sm line-clamp-2">{{ $media->title }}</h4>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-gray-500">{{ $media->user_ratings_count }}</span>
                                        @if($media->average_score)
                                            <span class="text-xs font-bold text-yellow-500">{{ number_format($media->average_score, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Series -->
            @if(isset($popularByCategory['series']) && $popularByCategory['series']->count() > 0)
                <section class="mb-12">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-800 pb-2">Series</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($popularByCategory['series'] as $media)
                            <a href="{{ route('media.show', $media->id) }}" class="bg-gray-900 rounded-lg overflow-hidden border border-gray-800 hover:border-blue-500 transition">
                                <img src="{{ $media->cover_url }}" alt="{{ $media->title }}" class="w-full h-56 object-cover">
                                <div class="p-2">
                                    <h4 class="font-bold text-sm line-clamp-2">{{ $media->title }}</h4>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-gray-500">{{ $media->user_ratings_count }}</span>
                                        @if($media->average_score)
                                            <span class="text-xs font-bold text-yellow-500">{{ number_format($media->average_score, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Videojuegos -->
            @if(isset($popularByCategory['game']) && $popularByCategory['game']->count() > 0)
                <section class="mb-12">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-800 pb-2">Videojuegos</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($popularByCategory['game'] as $media)
                            <a href="{{ route('media.show', $media->id) }}" class="bg-gray-900 rounded-lg overflow-hidden border border-gray-800 hover:border-blue-500 transition">
                                <img src="{{ $media->cover_url }}" alt="{{ $media->title }}" class="w-full h-56 object-cover">
                                <div class="p-2">
                                    <h4 class="font-bold text-sm line-clamp-2">{{ $media->title }}</h4>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-gray-500">{{ $media->user_ratings_count }}</span>
                                        @if($media->average_score)
                                            <span class="text-xs font-bold text-yellow-500">{{ number_format($media->average_score, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Libros -->
            @if(isset($popularByCategory['book']) && $popularByCategory['book']->count() > 0)
                <section class="mb-12">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-800 pb-2">Libros</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($popularByCategory['book'] as $media)
                            <a href="{{ route('media.show', $media->id) }}" class="bg-gray-900 rounded-lg overflow-hidden border border-gray-800 hover:border-blue-500 transition">
                                <img src="{{ $media->cover_url }}" alt="{{ $media->title }}" class="w-full h-56 object-cover">
                                <div class="p-2">
                                    <h4 class="font-bold text-sm line-clamp-2">{{ $media->title }}</h4>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-gray-500">{{ $media->user_ratings_count }}</span>
                                        @if($media->average_score)
                                            <span class="text-xs font-bold text-yellow-500">{{ number_format($media->average_score, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Sin contenido valorado -->
            @if(empty($popularByCategory) || collect($popularByCategory)->flatten()->isEmpty())
                <div class="text-center py-20">
                    <p class="text-gray-400 mb-4">No hay contenidos valorados aún.</p>
                    <a href="/" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded-lg">Explorar Contenido</a>
                </div>
            @endif
        </div>
    </main>
</body>
</html>
