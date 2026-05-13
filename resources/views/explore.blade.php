<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Explorar - {{ config('app.name', 'MyFicList') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,600,800&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #030712;
        }

        .glass {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            border-color: rgba(96, 165, 250, 0.5);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body class="bg-gray-950 text-gray-100 min-h-screen flex flex-col">
    @include('layouts.navigation')

    <main class="flex-grow py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header & Filters -->
            <div class="mb-12">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h1
                            class="text-4xl md:text-5xl font-black tracking-tighter bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                            Explorar Catálogo
                        </h1>
                        <p class="text-gray-400 mt-2 font-medium">Descubre todo el contenido guardado en nuestra base de
                            datos.</p>
                    </div>

                    <!-- Filters Form -->
                    <form action="{{ route('media.explore') }}" method="GET" class="flex flex-wrap items-center gap-3">
                        <div class="relative group">
                            <i
                                class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-400 transition-colors"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Buscar título..."
                                class="bg-gray-900/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all w-64">
                        </div>

                        <select name="type"
                            class="bg-gray-900/50 border border-white/10 rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500/50 outline-none transition-all cursor-pointer">
                            <option value="">Todos los tipos</option>
                            <option value="anime" {{ request('type') == 'anime' ? 'selected' : '' }}>Anime</option>
                            <option value="manga" {{ request('type') == 'manga' ? 'selected' : '' }}>Manga</option>
                            <option value="movie" {{ request('type') == 'movie' ? 'selected' : '' }}>Película</option>
                            <option value="series" {{ request('type') == 'series' ? 'selected' : '' }}>Serie</option>
                            <option value="game" {{ request('type') == 'game' ? 'selected' : '' }}>Videojuego</option>
                            <option value="book" {{ request('type') == 'book' ? 'selected' : '' }}>Libro</option>
                        </select>

                        <select name="genre"
                            class="bg-gray-900/50 border border-white/10 rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500/50 outline-none transition-all cursor-pointer">
                            <option value="">Todos los géneros</option>
                            @foreach($allGenres as $genre)
                                <option value="{{ $genre }}" {{ request('genre') == $genre ? 'selected' : '' }}>{{ $genre }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-blue-600/20 transition-all">
                            Filtrar
                        </button>

                        @if(request()->anyFilled(['search', 'type', 'genre']))
                            <a href="{{ route('media.explore') }}"
                                class="text-gray-500 hover:text-white transition-colors text-sm font-bold ml-2">
                                Limpiar
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Results Grid (Stable Grid System) -->
            <div id="media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-8">
                @forelse($mediaItems as $media)
                    @php
                        $extra = $media->extra_data ?? [];
                        $platforms = $extra['platforms'] ?? [];
                        $platformIcons = [];
                        foreach ($platforms as $p) {
                            $pLower = strtolower($p);
                            if (str_contains($pLower, 'pc') || str_contains($pLower, 'windows'))
                                $platformIcons[] = 'fab fa-windows';
                            if (str_contains($pLower, 'playstation') || str_contains($pLower, 'ps'))
                                $platformIcons[] = 'fab fa-playstation';
                            if (str_contains($pLower, 'xbox'))
                                $platformIcons[] = 'fab fa-xbox';
                            if (str_contains($pLower, 'nintendo') || str_contains($pLower, 'switch'))
                                $platformIcons[] = 'fab fa-nintendo-switch';
                        }
                        $platformIcons = array_unique($platformIcons);

                        $emoji = '';
                        // Use pre-loaded avg_score from withAvg, fall back to accessor
                        $displayScore = isset($media->avg_score) && $media->avg_score !== null
                            ? number_format((float) $media->avg_score, 1)
                            : null;
                    @endphp
                    <div class="flex flex-col h-full bg-gray-900/50 border border-white/5 rounded-[2rem] overflow-hidden hover:border-blue-500/30 transition-all duration-300 group shadow-2xl">
                        <!-- Image Container -->
                        <a href="{{ route('media.show', $media->id) }}" class="block relative aspect-[2/3] overflow-hidden">
                            <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                class="w-full h-full object-cover brightness-90 group-hover:brightness-110 group-hover:scale-110 transition-all duration-700">
                            
                            @if($displayScore)
                                <div class="absolute top-4 right-4 bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/10 text-yellow-400 text-[10px] font-black flex items-center gap-1.5 shadow-lg">
                                    <i class="fas fa-star text-[9px]"></i> {{ $displayScore }}
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 flex flex-col flex-grow">
                            @if(!empty($platformIcons))
                                <div class="flex items-center gap-2 mb-3">
                                    @foreach($platformIcons as $icon)
                                        <i class="{{ $icon }} text-blue-500/70 text-[10px]"></i>
                                    @endforeach
                                </div>
                            @endif

                            <h3 class="text-white font-bold text-sm leading-snug mb-4 line-clamp-2 group-hover:text-blue-400 transition-colors">
                                {{ $media->title }}
                            </h3>

                            <!-- Footer Actions -->
                            <div class="mt-auto pt-4 border-t border-white/5 flex items-center justify-between">
                                @auth
                                    <button onclick="openListModal({{ $media->id }})"
                                        class="text-[10px] font-black text-blue-500 hover:text-blue-400 uppercase tracking-widest flex items-center gap-2 transition-colors">
                                        <i class="fas fa-plus-circle"></i> Agregar
                                    </button>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="text-[10px] font-black text-blue-500 hover:text-blue-400 uppercase tracking-widest flex items-center gap-2 transition-colors">
                                        <i class="fas fa-plus-circle"></i> Agregar
                                    </a>
                                @endauth
                                
                                <a href="{{ route('media.show', $media->id) }}"
                                    class="text-[10px] font-black text-gray-500 hover:text-white uppercase tracking-widest flex items-center gap-2 transition-colors">
                                    Detalles <i class="fas fa-arrow-right text-[8px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <div
                            class="w-20 h-20 bg-slate-900 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-blue-900/20">
                            <i class="fas fa-search text-3xl text-slate-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-300">No se encontraron resultados</h3>
                        <p class="text-gray-500 mt-2">Prueba ajustando los filtros de búsqueda.</p>
                        <a href="{{ route('media.explore') }}"
                            class="mt-6 inline-block bg-blue-600 text-white font-bold px-8 py-3 rounded-2xl hover:bg-blue-500 transition-colors">
                            Ver todo el catálogo
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination (Hidden for Infinite Scroll) -->
            <div id="pagination-container" class="mt-16 hidden">
                {{ $mediaItems->links() }}
            </div>

            <!-- Loading Indicator -->
            <div id="loading-indicator" class="mt-12 hidden flex justify-center pb-12">
                <i class="fas fa-circle-notch fa-spin text-4xl text-blue-500"></i>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div id="list-modal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md hidden">
        <div class="glass max-w-md w-full p-10 rounded-3xl">
            <h3 class="text-3xl font-black mb-8">Agregar a mi lista</h3>
            <form action="{{ route('user-list.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="media_id" id="modal-media-id" value="">

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Lista</label>
                    <select name="media_list_id" onchange="toggleNewListForm(this.value)"
                        class="w-full bg-gray-800 border-none rounded-xl p-4 text-white font-bold focus:ring-2 focus:ring-purple-600">
                        <option value="">Mi Lista (Predeterminada)</option>
                        @auth
                            @foreach($mediaLists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }}
                                    {{ $list->is_public ? '(Pública)' : '(Privada)' }}
                                </option>
                            @endforeach
                        @endauth
                        <option value="new">+ Crear nueva lista</option>
                    </select>

                    <div id="new-list-fields"
                        class="hidden mt-3 p-4 bg-gray-900/50 rounded-xl border border-gray-700/50 space-y-3">
                        <input type="text" name="new_list_name" placeholder="Nombre de la nueva lista..."
                            class="w-full bg-gray-800 border-none rounded-lg p-3 text-white font-bold focus:ring-2 focus:ring-purple-600 text-sm">
                        <label class="flex items-center gap-3 cursor-pointer group w-fit">
                            <div class="relative">
                                <input type="checkbox" name="is_public" value="1" class="sr-only peer">
                                <div
                                    class="w-9 h-5 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-500">
                                </div>
                            </div>
                            <span
                                class="text-xs font-black text-gray-400 uppercase tracking-wider group-hover:text-white transition-colors">Hacer
                                Pública</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Estado</label>
                    <select name="status"
                        class="w-full bg-gray-800 border-none rounded-xl p-4 text-white font-bold focus:ring-2 focus:ring-purple-600">
                        <option value="watching">Viendo / Jugando</option>
                        <option value="completed">Completado</option>
                        <option value="on_hold">En Pausa</option>
                        <option value="dropped">Abandonado</option>
                        <option value="plan_to_watch">Pendiente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Puntuación
                        (1-10)</label>
                    <select name="score"
                        class="w-full bg-gray-800 border-none rounded-xl p-4 text-white font-bold focus:ring-2 focus:ring-purple-600">
                        <option value="">Sin nota</option>
                        @for($i = 10; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ $i }} -
                                {{ $i == 10 ? 'Obra Maestra' : ($i >= 8 ? 'Muy Bueno' : ($i >= 5 ? 'Aceptable' : 'Pobre')) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="document.getElementById('list-modal').classList.add('hidden')"
                        class="flex-1 py-4 bg-gray-800 text-white font-bold rounded-xl hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit"
                        class="flex-1 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] transition-all">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Logic
        function openListModal(mediaId) {
            document.getElementById('modal-media-id').value = mediaId;
            document.getElementById('list-modal').classList.remove('hidden');
        }

        function toggleNewListForm(value) {
            const fields = document.getElementById('new-list-fields');
            if (value === 'new') {
                fields.classList.remove('hidden');
            } else {
                fields.classList.add('hidden');
            }
        }

        // Infinite Scroll Logic
        let nextPageUrl = '{!! $mediaItems->nextPageUrl() !!}';
        let isLoading = false;
        const mediaGrid = document.getElementById('media-grid');
        const loadingIndicator = document.getElementById('loading-indicator');

        if (mediaGrid) {
            window.addEventListener('scroll', () => {
                if (isLoading || !nextPageUrl) return;

                // Trigger load when 800px from the bottom
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 800) {
                    loadMore();
                }
            });
        }

        async function loadMore() {
            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                const response = await fetch(nextPageUrl);
                const html = await response.text();

                // Parse the loaded HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Extract new items
                const newItems = doc.querySelectorAll('#media-grid > div');
                newItems.forEach(item => {
                    mediaGrid.appendChild(item);
                });

                // Update next page URL by looking for the "next" rel link in the new document
                const nextLink = doc.querySelector('a[rel="next"]');
                if (nextLink) {
                    nextPageUrl = nextLink.href;
                } else {
                    nextPageUrl = null; // No more pages available
                }
            } catch (e) {
                console.error('Error loading more media:', e);
            }

            isLoading = false;
            loadingIndicator.classList.add('hidden');
        }
    </script>

    @include('layouts.footer')
</body>

</html>