<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFicList - Buscador Universal de Entretenimiento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js for mobile menu responsiveness -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        }

        .category-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-8px);
        }

        .category-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.1) 100%);
        }
    </style>
</head>

<body class="bg-gray-950 text-white min-h-screen flex flex-col">
    @include('layouts.navigation')

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="hero-gradient min-h-[80vh] flex items-center justify-center px-4 py-20">
            <div class="max-w-4xl w-full text-center">
                <!-- Main Title -->
                <div class="mb-8">
                    <h1
                        class="text-6xl md:text-8xl font-black mb-6 pb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400">
                        MyFicList
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-400 mb-4">
                        Tu biblioteca universal de entretenimiento
                    </p>
                    <p class="text-gray-500">
                        Anime, Manga, Películas, Series de TV y Videojuegos en un solo lugar
                    </p>
                </div>

                <!-- Search Bar -->
                <form action="{{ url('/search/unified') }}" method="GET"
                    class="bg-gray-900/50 backdrop-blur-sm p-8 rounded-2xl shadow-2xl border border-gray-800 mb-12">
                    <div class="flex flex-col lg:flex-row gap-4 items-stretch">
                        <input type="text" name="query" placeholder="¿Qué quieres descubrir hoy?" required
                            class="flex-grow bg-gray-800 border border-gray-700 rounded-lg px-6 py-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-lg">

                        <select name="type"
                            class="bg-gray-800 border border-gray-700 rounded-lg px-6 py-4 text-white outline-none cursor-pointer focus:ring-2 focus:ring-blue-500 transition">
                            <option value="all">🔍 Todo</option>
                            <option value="anime">🍙 Anime</option>
                            <option value="manga">📖 Manga</option>
                            <option value="peli">🎬 Película</option>
                            <option value="serie">📺 Serie TV</option>
                            <option value="game">🎮 Videojuego</option>
                            <option value="book">📚 Libro</option>
                        </select>

                        <label class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer">
                            <input type="checkbox" name="safe" value="1"
                                class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                            +18
                        </label>

                        <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold px-8 py-4 rounded-lg transition-all shadow-lg">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </form>

                <!-- Quick Categories -->
                <div class="text-gray-400 text-sm mb-8">
                    O explora por categoría:
                </div>
                <div class="flex flex-wrap justify-center gap-3 mb-16">
                    <a href="{{ route('media.explore') }}"
                        class="bg-gray-900/80 hover:bg-gray-800 text-blue-400 border border-blue-500/30 rounded-lg py-3 px-6 transition text-sm font-black uppercase tracking-widest"><i
                            class="fas fa-search mr-2"></i>Explorar Todo</a>
                    <a href="{{ route('dashboard') }}"
                        class="bg-gray-900/80 hover:bg-gray-800 text-purple-400 border border-purple-500/30 rounded-lg py-3 px-6 transition text-sm font-black uppercase tracking-widest"><i
                            class="fas fa-award mr-2"></i>Fiction Top</a>
                    @auth
                        <a href="{{ route('user-list.index') }}"
                            class="bg-gray-900/80 hover:bg-gray-800 text-green-400 border border-green-500/30 rounded-lg py-3 px-6 transition text-sm font-black uppercase tracking-widest"><i
                                class="fas fa-list mr-2"></i>Mi Lista</a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="max-w-7xl mx-auto px-4 py-20">
            <h2 class="text-4xl font-bold mb-12 text-center">Características Principales</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 hover:border-blue-500 transition">
                    <div class="text-4xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold mb-3">Búsqueda inteligente</h3>
                    <p class="text-gray-400">Busca en múltiples bases de datos: Jikan, TMDB, RAWG y más. Resultados
                        precisos y rápidos.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 hover:border-purple-500 transition">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-xl font-bold mb-3">Organiza tu colección</h3>
                    <p class="text-gray-400">Crea listas personalizadas: En progreso, Completado, Pendiente, etc.
                        Califica y
                        trackea tu progreso.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 hover:border-pink-500 transition">
                    <div class="text-4xl mb-4">🎯</div>
                    <h3 class="text-xl font-bold mb-3">Todo en Uno</h3>
                    <p class="text-gray-400">Anime, Manga, Películas, Series, Videojuegos. Tu biblioteca de
                        entretenimiento unificada.</p>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="max-w-7xl mx-auto px-4 py-20">
            <h2 class="text-4xl font-bold mb-12 text-center">Explora por tipo</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <!-- Anime -->
                <a href="{{ route('media.explore', ['type' => 'anime']) }}" class="category-card group">
                    <div
                        class="bg-gradient-to-br from-purple-900/30 to-purple-900/10 border border-purple-800/50 rounded-xl overflow-hidden h-40 flex items-center justify-center hover:border-purple-500 transition">
                        <div class="text-center">
                            <div class="text-5xl mb-2">🍙</div>
                            <p class="text-lg font-bold group-hover:text-purple-300 transition">Anime</p>
                        </div>
                    </div>
                </a>

                <!-- Manga -->
                <a href="{{ route('media.explore', ['type' => 'manga']) }}" class="category-card group">
                    <div
                        class="bg-gradient-to-br from-blue-900/30 to-blue-900/10 border border-blue-800/50 rounded-xl overflow-hidden h-40 flex items-center justify-center hover:border-blue-500 transition">
                        <div class="text-center">
                            <div class="text-5xl mb-2">📖</div>
                            <p class="text-lg font-bold group-hover:text-blue-300 transition">Manga</p>
                        </div>
                    </div>
                </a>

                <!-- Movies -->
                <a href="{{ route('media.explore', ['type' => 'peli']) }}" class="category-card group">
                    <div
                        class="bg-gradient-to-br from-red-900/30 to-red-900/10 border border-red-800/50 rounded-xl overflow-hidden h-40 flex items-center justify-center hover:border-red-500 transition">
                        <div class="text-center">
                            <div class="text-5xl mb-2">🎬</div>
                            <p class="text-lg font-bold group-hover:text-red-300 transition">Películas</p>
                        </div>
                    </div>
                </a>

                <!-- Series -->
                <a href="{{ route('media.explore', ['type' => 'serie']) }}" class="category-card group">
                    <div
                        class="bg-gradient-to-br from-green-900/30 to-green-900/10 border border-green-800/50 rounded-xl overflow-hidden h-40 flex items-center justify-center hover:border-green-500 transition">
                        <div class="text-center">
                            <div class="text-5xl mb-2">📺</div>
                            <p class="text-lg font-bold group-hover:text-green-300 transition">Series TV</p>
                        </div>
                    </div>
                </a>

                <!-- Games -->
                <a href="{{ route('media.explore', ['type' => 'game']) }}" class="category-card group">
                    <div
                        class="bg-gradient-to-br from-yellow-900/30 to-yellow-900/10 border border-yellow-800/50 rounded-xl overflow-hidden h-40 flex items-center justify-center hover:border-yellow-500 transition">
                        <div class="text-center">
                            <div class="text-5xl mb-2">🎮</div>
                            <p class="text-lg font-bold group-hover:text-yellow-300 transition">Videojuegos</p>
                        </div>
                    </div>
                </a>

                <!-- Books -->
                <a href="{{ route('media.explore', ['type' => 'book']) }}" class="category-card group">
                    <div
                        class="bg-gradient-to-br from-indigo-900/30 to-indigo-900/10 border border-indigo-800/50 rounded-xl overflow-hidden h-40 flex items-center justify-center hover:border-indigo-500 transition">
                        <div class="text-center">
                            <div class="text-5xl mb-2">📚</div>
                            <p class="text-lg font-bold group-hover:text-indigo-300 transition">Libros</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-blue-900/20 to-purple-900/20 border-t border-gray-800 py-16 mt-20">
            <div class="max-w-4xl mx-auto text-center px-4">
                @guest
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">¿Listo para organizar tu colección?</h2>
                    <p class="text-gray-400 mb-8">Únete a miles de usuarios que ya están usando MyFicList para gestionar su
                        entretenimiento.</p>
                    <a href="{{ route('register') }}"
                        class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold px-8 py-4 rounded-lg transition-all shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i>Crear cuenta
                    </a>
                @endguest
                @auth
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">¡Hola, {{ auth()->user()->name }}!</h2>
                    <p class="text-gray-400 mb-8">Tienes nuevos títulos esperándote. Gestiona tu lista, añade reseñas y
                        descubre qué ver a continuación.</p>
                    <div class="flex flex-wrap gap-4 justify-center">
                        <a href="{{ route('media.explore') }}"
                            class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold px-8 py-4 rounded-lg transition-all shadow-lg">
                            <i class="fas fa-search mr-2"></i>Buscar contenido
                        </a>
                        <a href="{{ route('user-list.index') }}"
                            class="inline-block bg-gray-800 hover:bg-gray-700 text-white font-bold px-8 py-4 rounded-lg transition-all border border-gray-700">
                            <i class="fas fa-list mr-2"></i>Mi lista
                        </a>
                    </div>
                @endauth
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('layouts.footer')
    @include('components.search-loading')
</body>

</html>