<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Resultados de Búsqueda</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-950 text-gray-100 min-h-screen flex flex-col">
    @include('layouts.navigation')

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold mb-8">Resultados de Búsqueda</h1>

            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-green-700 bg-green-900/70 p-4 text-green-100">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 rounded-2xl border border-red-700 bg-red-900/70 p-4 text-red-100">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $grouped = collect($results)->groupBy('media_type');
                $labels = [
                    'anime' => 'Anime',
                    'manga' => 'Manga',
                    'peli' => 'Películas',
                    'serie' => 'Series',
                    'game' => 'Videojuegos',
                    'book' => 'Novelas',
                ];
            @endphp

            <!-- Filtros por categoría -->
            @if(!$grouped->isEmpty())
                <div class="mb-8 flex flex-wrap gap-2">
                    <button onclick="filterByCategory('all')"
                        class="category-filter active bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium"
                        data-category="all">
                        <i class="fas fa-th mr-2"></i>Ver todo
                    </button>
                    @foreach($labels as $typeKey => $label)
                        @if(isset($grouped[$typeKey]) && $grouped[$typeKey]->isNotEmpty())
                            <button onclick="filterByCategory('{{ $typeKey }}')"
                                class="category-filter bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded-lg transition font-medium"
                                data-category="{{ $typeKey }}">
                                <i class="fas fa-filter mr-2"></i>{{ $label }}
                            </button>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($grouped->isEmpty())
                <div class="text-center py-12">
                    <div class="text-gray-400 text-lg mb-4">
                        <i class="fas fa-search text-4xl mb-4 block"></i>
                        No se encontraron resultados
                    </div>
                    <p class="text-gray-500">Intenta con otros términos de búsqueda o verifica tu conexión a internet.</p>
                </div>
            @else
                @foreach($labels as $typeKey => $label)
                    @if(isset($grouped[$typeKey]) && $grouped[$typeKey]->isNotEmpty())
                        <section class="mb-10 category-section" data-category="{{ $typeKey }}">
                            <div class="mb-4 flex items-center justify-between">
                                <h2 class="text-2xl font-semibold text-gray-100">{{ $label }}</h2>
                                <span class="text-sm text-gray-400">{{ $grouped[$typeKey]->count() }} resultados</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($grouped[$typeKey] as $result)
                                    <div
                                        class="bg-gray-900 rounded-lg overflow-hidden hover:shadow-xl transition-shadow flex flex-col h-full">
                                        <div class="bg-gray-800 overflow-hidden h-80 flex items-center justify-center">
                                            @if($result['cover_url'])
                                                <img src="{{ $result['cover_url'] }}" class="w-full h-full object-cover"
                                                    alt="{{ $result['title'] }}"
                                                    onerror="this.onerror=null; this.src='https://placehold.co/400x600/1f2937/9ca3af?text=Sin+Imagen';">
                                            @else
                                                <div class="flex flex-col items-center justify-center text-gray-500 p-4 text-center">
                                                    <i class="fas fa-image text-4xl mb-2"></i>
                                                    <span class="text-xs font-medium">Imagen no disponible</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-3 flex-1 flex flex-col justify-between">
                                            <div>
                                                @if($result['source'] !== 'Local')
                                                    <span
                                                        class="text-xs font-bold uppercase text-blue-400 bg-blue-900/30 px-2 py-1 rounded">
                                                        {{ $result['source'] }}
                                                    </span>
                                                @endif

                                                {{--
                                                He cambiado ml-4 por ml-1 (margen externo sutil)
                                                y he añadido pl-2 (espacio interno antes del texto)
                                                --}}
                                                <h3 class="font-bold text-xs text-gray-100 mt-2 line-clamp-2 ml-1 pl-2">
                                                    {{ $result['title'] }}
                                                </h3>
                                            </div>

                                            <div class="mt-4 space-y-3">
                                                <a href="{{ route('media.details', ['external_id' => $result['external_id'], 'source' => $result['source'], 'type' => $result['media_type']]) }}"
                                                    class="block text-center bg-white/10 hover:bg-white/20 text-white py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                                    <i class="fas fa-info-circle mr-2 text-blue-400"></i>Detalles
                                                </a>

                                                @auth
                                                    @php
                                                        $total = match ($result['media_type']) {
                                                            'anime', 'serie', 'series' => $result['episodes'] ?? 0,
                                                            'manga', 'book' => $result['chapters'] ?? 0,
                                                            default => null,
                                                        };
                                                    @endphp
                                                    <button type="button"
                                                        onclick="openAddModal('{{ $result['external_id'] }}', '{{ $result['source'] }}', '{{ $result['media_type'] }}', '{{ addslashes($result['title']) }}', {{ $total ?? 'null' }})"
                                                        class="w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] transition-all uppercase text-[10px] tracking-widest">
                                                        <i class="fas fa-plus mr-2"></i>Agregar
                                                    </button>
                                                @else
                                                    <a href="{{ route('login') }}"
                                                        class="block text-center py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] transition-all uppercase text-[10px] tracking-widest">
                                                        <i class="fas fa-plus mr-2"></i>Agregar
                                                    </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            @endif
        </div>

    <!-- Modal para agregar a lista -->
    <div id="list-modal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md hidden">
        <div class="glass max-w-md w-full p-10 rounded-3xl">
            <h3 class="text-3xl font-black mb-2 uppercase tracking-tighter">A mi lista</h3>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-8" id="modalTitleDisplay"></p>
            
            <form action="{{ route('user-list.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="external_id" id="modalExternalId">
                <input type="hidden" name="source" id="modalSource">
                <input type="hidden" name="media_type" id="modalMediaType">

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
                        class="flex-1 py-4 bg-gray-800 text-white font-bold rounded-xl hover:bg-gray-700 transition-colors uppercase text-xs tracking-widest">Cancelar</button>
                    <button type="submit"
                        class="flex-1 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] transition-all uppercase text-xs tracking-widest">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    </main>
    @include('layouts.footer')
    @include('components.search-loading')

    <script>
        let currentFilter = 'all';

        function filterByCategory(category) {
            currentFilter = category;
            const sections = document.querySelectorAll('.category-section');
            const buttons = document.querySelectorAll('.category-filter');

            sections.forEach(section => {
                if (category === 'all' || section.dataset.category === category) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });

            buttons.forEach(btn => {
                if (btn.dataset.category === category) {
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    btn.classList.remove('bg-gray-700', 'hover:bg-gray-600', 'text-gray-200');
                    btn.classList.add('text-white');
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    btn.classList.add('bg-gray-700', 'hover:bg-gray-600', 'text-gray-200');
                    btn.classList.remove('text-white');
                    btn.classList.remove('active');
                }
            });
        }

        function openAddModal(externalId, source, mediaType, title, maxProgress) {
            document.getElementById('modalTitleDisplay').textContent = title;
            document.getElementById('modalExternalId').value = externalId;
            document.getElementById('modalSource').value = source;
            document.getElementById('modalMediaType').value = mediaType;
            
            // Note: maxProgress logic was in previous script but new modal doesn't use it yet
            // If needed, we can add a progress field to the modal similar to the score field
            
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

        // Cerrar modal al hacer clic fuera
        document.getElementById('list-modal').addEventListener('click', function (e) {
            if (e.target === this) {
                document.getElementById('list-modal').classList.add('hidden');
            }
        });
    </script>
</body>

</html>