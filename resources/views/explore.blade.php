<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Explorar</title>
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
                            class="text-4xl md:text-5xl font-black tracking-tighter bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent pb-2">
                            Explorar catálogo
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
                                class="bg-gray-900/50 rounded-2xl py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all w-64">
                        </div>

                        <select name="type"
                            class="bg-gray-900/50 rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500/50 outline-none transition-all cursor-pointer">
                            <option value="">Todos los tipos</option>
                            <option value="anime" {{ request('type') == 'anime' ? 'selected' : '' }}>Anime</option>
                            <option value="manga" {{ request('type') == 'manga' ? 'selected' : '' }}>Manga</option>
                            <option value="peli" {{ request('type') == 'peli' || request('type') == 'movie' ? 'selected' : '' }}>Película</option>
                            <option value="serie" {{ request('type') == 'serie' || request('type') == 'series' ? 'selected' : '' }}>Serie</option>
                            <option value="game" {{ request('type') == 'game' ? 'selected' : '' }}>Videojuego</option>
                            <option value="book" {{ request('type') == 'book' ? 'selected' : '' }}>Libro</option>
                        </select>

                        <select name="genre"
                            class="bg-gray-900/50 rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500/50 outline-none transition-all cursor-pointer">
                            <option value="">Todos los géneros</option>
                            @foreach($allGenres as $genre)
                                <option value="{{ $genre }}" {{ request('genre') == $genre ? 'selected' : '' }}>{{ $genre }}
                                </option>
                            @endforeach
                        </select>

                        <div id="platform-filter-container" class="hidden">
                            <select name="platform"
                                class="bg-gray-900/50 rounded-2xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500/50 outline-none transition-all cursor-pointer">
                                <option value="">Todas las plataformas</option>
                                @foreach($allPlatforms as $platform)
                                    <option value="{{ $platform }}" {{ request('platform') == $platform ? 'selected' : '' }}>{{ $platform }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-blue-600/20 transition-all">
                            Filtrar
                        </button>

                        @if(request()->anyFilled(['search', 'type', 'genre', 'platform']))
                            <a href="{{ route('media.explore') }}"
                                class="text-gray-500 hover:text-white transition-colors text-sm font-bold ml-2">
                                Limpiar
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Results Grid (Precise Masonry) -->
            @if($mediaItems->isEmpty())
                <div class="py-24 text-center w-full">
                    <h3 class="text-xl font-bold text-gray-300">No se encontraron resultados</h3>
                    <p class="text-gray-500 mt-2">Prueba ajustando los filtros de búsqueda.</p>
                </div>
            @else
                <div id="media-grid" class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-6">
                    @foreach($mediaItems as $media)
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
                        <div class="break-inside-avoid mb-6">
                            <div
                                class="bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.02] transform-gpu flex flex-col">

                                <!-- Image Container linked to details -->
                                <a href="{{ route('media.show', $media->id) }}" class="block relative group cursor-pointer">
                                    <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                        class="w-full h-auto object-cover brightness-90 group-hover:brightness-100 transition-all rounded-t-2xl">

                                    @if($displayScore)
                                        <div style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.75); border-radius: 0.5rem; padding: 0.25rem 0.5rem; z-index: 10; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;"
                                            class="backdrop-blur-sm shadow-lg text-yellow-400 text-xs font-black">
                                            <i class="fas fa-star text-[9px]"></i> {{ $displayScore }}
                                        </div>
                                    @endif


                                    @if(!empty($extra['trailer_url']))
                                        <div
                                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/40 backdrop-blur-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fas fa-play text-white text-xs ml-1"></i>
                                        </div>
                                    @endif
                                </a>

                                <!-- Content -->
                                <div class="p-5 flex-grow flex flex-col">
                                    @if(!empty($platformIcons))
                                        <!-- Platforms -->
                                        <div class="flex items-center gap-2 mb-3">
                                            @foreach($platformIcons as $icon)
                                                <i class="{{ $icon }} text-blue-400 text-[11px]"></i>
                                            @endforeach
                                        </div>
                                    @endif

                                    <h3 class="font-bold text-lg text-white leading-tight mb-0">
                                        {{ $media->title }}
                                    </h3>

                                    <!-- Action Buttons -->
                                    <div class="mt-auto flex flex-wrap items-center justify-between gap-2 pt-4">
                                        @auth
                                            <button onclick="openListModal({{ $media->id }})"
                                                class="text-blue-400 hover:text-blue-300 flex items-center gap-1.5 text-xs font-bold transition-colors">
                                                <i class="fas fa-plus-circle"></i> Agregar
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}"
                                                class="text-blue-400 hover:text-blue-300 flex items-center gap-1.5 text-xs font-bold transition-colors">
                                                <i class="fas fa-plus-circle"></i> Agregar
                                            </a>
                                        @endauth
                                        <a href="{{ route('media.show', $media->id) }}"
                                            class="text-gray-400 hover:text-white flex items-center gap-1.5 text-xs font-bold transition-colors">
                                            Detalles <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

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
                        <option value="">Mi lista</option>
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
                        class="hidden mt-3 p-4 bg-gray-900/50 rounded-xl space-y-3">
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
                        <option value="watching">En progreso</option>
                        <option value="completed">Completado</option>
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
        // Platform Filter Toggle Logic
        function togglePlatformFilter() {
            const typeSelect = document.querySelector('select[name="type"]');
            const platformSelect = document.getElementById('platform-filter-container');
            if (typeSelect && platformSelect) {
                if (typeSelect.value === 'game') {
                    platformSelect.classList.remove('hidden');
                } else {
                    platformSelect.classList.add('hidden');
                    const platformInput = platformSelect.querySelector('select');
                    if (platformInput) platformInput.value = '';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const typeSelect = document.querySelector('select[name="type"]');
            if (typeSelect) {
                typeSelect.addEventListener('change', togglePlatformFilter);
                togglePlatformFilter();
            }
        });

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