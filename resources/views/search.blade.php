<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Resultados de Búsqueda - {{ config('app.name', 'MyFicList') }}</title>
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
                'movie' => 'Películas',
                'series' => 'Series',
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
                                            <img src="{{ $result['cover_url'] }}" 
                                                class="w-full h-full object-cover"
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
                                                <span class="text-xs font-bold uppercase text-blue-400 bg-blue-900/30 px-2 py-1 rounded">
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

                                        <div class="mt-3 space-y-2">
                                            <a href="{{ route('media.details', ['external_id' => $result['external_id'], 'source' => $result['source'], 'type' => $result['media_type']]) }}"
                                                class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded text-xs transition-colors font-medium">
                                                <i class="fas fa-info-circle mr-1"></i>Ver
                                            </a>

                                            @auth
                                                @php
                                                    $total = match($result['media_type']) {
                                                        'anime', 'series' => $result['episodes'] ?? 0,
                                                        'manga', 'book' => $result['chapters'] ?? 0,
                                                        default => null,
                                                    };
                                                @endphp
                                                <button type="button"
                                                    onclick="openAddModal('{{ $result['external_id'] }}', '{{ $result['source'] }}', '{{ $result['media_type'] }}', '{{ addslashes($result['title']) }}', {{ $total ?? 'null' }})"
                                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white py-1 px-3 rounded text-xs transition-colors font-medium">
                                                    <i class="fas fa-plus mr-1"></i>Agregar
                                                </button>
                                            @else
                                                <a href="{{ route('login') }}"
                                                    class="block text-center bg-purple-600 hover:bg-purple-700 text-white py-1 px-3 rounded text-xs transition-colors font-medium">
                                                    <i class="fas fa-plus mr-1"></i>Agregar
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
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-90 hidden z-50"
        style="background-color: rgba(0,0,0,0.92);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg shadow-xl max-w-md w-full border border-gray-700"
                style="background-color:#111827;color:#f8fafc;">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-100 mb-4">Agregar a tu lista</h3>
                    <p class="text-gray-300 mb-4" id="modalTitle"></p>

                    <form id="addForm" action="{{ route('user-list.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="external_id" id="modalExternalId">
                        <input type="hidden" name="source" id="modalSource">
                        <input type="hidden" name="media_type" id="modalMediaType">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Estado</label>
                            <select name="status"
                                class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500"
                                style="background-color:#1f2937;color:#f8fafc;">
                                <option value="watching">En progreso</option>
                                <option value="completed">Finalizado</option>
                                <option value="plan_to_watch">A futuro</option>
                                <option value="dropped">Eliminado</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Puntuación (opcional)</label>
                            <select name="score"
                                class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500"
                                style="background-color:#1f2937;color:#f8fafc;">
                                <option value="">Sin puntuación</option>
                                <option value="0">0 - No me gusta</option>
                                <option value="10">10 - Excelente</option>
                                <option value="9">9 - Muy bueno</option>
                                <option value="8">8 - Bueno</option>
                                <option value="7">7 - Regular</option>
                                <option value="6">6 - Pasable</option>
                                <option value="5">5 - Normal</option>
                                <option value="4">4 - Por debajo</option>
                                <option value="3">3 - Malo</option>
                                <option value="2">2 - Muy malo</option>
                                <option value="1">1 - Terrible</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Progreso (capítulos/páginas vistos)</label>
                            <input type="number" id="modalProgress" name="progress" min="0" value="0"
                                class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500"
                                style="background-color:#1f2937;color:#f8fafc;" placeholder="0">
                        </div>

                        <div class="flex space-x-3">
                            <button type="button" onclick="closeAddModal()"
                                class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition-colors"
                                style="background-color:#374151;">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition-colors"
                                style="background-color:#7c3aed;">
                                Agregar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </main>
    @include('layouts.footer')

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
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalExternalId').value = externalId;
            document.getElementById('modalSource').value = source;
            document.getElementById('modalMediaType').value = mediaType;
            const progressInput = document.getElementById('modalProgress');
            if (maxProgress !== null && maxProgress > 0) {
                progressInput.setAttribute('max', maxProgress);
            } else {
                progressInput.removeAttribute('max');
            }
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('addModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeAddModal();
            }
        });
    </script>
</body>

</html>