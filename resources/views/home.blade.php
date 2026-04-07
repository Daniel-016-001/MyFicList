<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MyHub - Buscador Universal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full px-4 text-center">
        <h1 class="text-6xl font-extrabold text-yellow-500 mb-2">MyHub</h1>
        <p class="text-gray-400 mb-8 italic">Anime, Cine, Series y Videojuegos en un solo lugar</p>

        <form action="{{ url('/search') }}" method="GET" class="bg-gray-800 p-6 rounded-2xl shadow-2xl border border-gray-700">
            <div class="flex flex-col md:flex-row gap-4">
                <input type="text" name="q" placeholder="¿Qué quieres buscar hoy?" required
                    class="flex-grow bg-gray-700 border-none rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-yellow-500 outline-none">
                
                <select name="type" class="bg-gray-700 border-none rounded-xl px-4 py-3 text-white outline-none cursor-pointer">
                    <option value="anime">Anime</option>
                    <option value="movie">Película</option>
                    <option value="series">Serie TV</option>
                    <option value="game">Videojuego</option>
                    <option value="manga">Manga</option>
                </select>

                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold px-8 py-3 rounded-xl transition-all">
                    Buscar
                </button>
            </div>
        </form>
        
        <div class="mt-6 text-sm text-gray-500">
            Busca y añade contenido a tu lista personal
        </div>
    </div>
</body>
</html>