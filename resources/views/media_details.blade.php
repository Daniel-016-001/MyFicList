<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ data_get($details, 'title', 'Detalle') }} - {{ config('app.name', 'MyFicList') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-950 text-gray-100 min-h-screen flex flex-col">
    @include('layouts.navigation')

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Imagen y acciones -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8">
                        <img src="{{ data_get($details, 'cover_url') }}" alt="{{ data_get($details, 'title') }}"
                            class="w-full max-w-[280px] lg:max-w-full mx-auto lg:mx-0 rounded-lg shadow-lg mb-6 block object-contain">

                        <div class="space-y-3">
                            <a href="{{ url()->previous() }}"
                                class="block text-center bg-gray-700 hover:bg-gray-600 text-white py-3 px-4 rounded-lg transition-colors font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>Volver
                            </a>

                            @auth
                                @php
                                    $mediaType = strtolower(data_get($details, 'type'));
                                    $total = match ($mediaType) {
                                        'anime', 'series' => data_get($details, 'episodes', 0),
                                        'manga', 'book' => data_get($details, 'chapters', 0),
                                        default => null,
                                    };
                                @endphp
                                <button type="button"
                                    onclick="openAddModal('{{ data_get($details, 'external_id') }}', '{{ data_get($details, 'source') }}', '{{ $mediaType }}', '{{ addslashes(data_get($details, 'title')) }}', {{ $total ?? 'null' }})"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg transition-colors font-medium">
                                    <i class="fas fa-plus mr-2"></i>Agregar a lista
                                </button>
                            @else
                                <a href="{{ route('login') }}"
                                    class="block text-center bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg transition-colors font-medium">
                                    <i class="fas fa-plus mr-2"></i>Agregar a lista
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Información detallada -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-900 rounded-xl p-8 border border-gray-800 shadow-2xl">
                        @if(!empty(data_get($details, 'trailer_url')))
                            <div class="mb-10">
                                @php
                                    $trailerUrl = data_get($details, 'trailer_url');
                                    $isYoutube = !empty($trailerUrl) && (str_contains($trailerUrl, 'youtube.com') || str_contains($trailerUrl, 'youtu.be'));
                                    $isDirectVideo = !empty($trailerUrl) && (str_ends_with(strtolower($trailerUrl), '.mp4') || str_contains($trailerUrl, 'video.rawg.io'));
                                    $youtubeId = null;
                                    if ($isYoutube) {
                                        if (preg_match('/(?:v=|\/embed\/|youtu\.be\/)([A-Za-z0-9_-]{11})/', $trailerUrl, $matches)) {
                                            $youtubeId = $matches[1];
                                        }
                                    }
                                @endphp

                                @if($youtubeId)
                                    <div
                                        class="aspect-video rounded-2xl overflow-hidden border border-gray-700 bg-black shadow-2xl">
                                        <iframe class="w-full h-full"
                                            src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=0&rel=0"
                                            title="Tráiler de {{ data_get($details, 'title') }}" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                    </div>
                                @elseif($isDirectVideo)
                                    <div
                                        class="aspect-video rounded-2xl overflow-hidden shadow-2xl border border-gray-800 bg-black">
                                        <video class="w-full h-full" controls preload="metadata">
                                            <source src="{{ $trailerUrl }}" type="video/mp4">
                                            Tu navegador no soporta el elemento de video.
                                        </video>
                                    </div>
                                @else
                                    <div
                                        class="aspect-video rounded-2xl flex flex-col items-center justify-center p-8 bg-gray-800/50 border-2 border-gray-700 border-dashed transition-all hover:bg-gray-800 hover:border-purple-500 group">
                                        <div class="mb-4 text-purple-400 group-hover:scale-110 transition-transform">
                                            <i class="fab fa-youtube text-6xl"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-white mb-2">Tráiler no disponible para reproducción
                                            directa</h3>
                                        <p class="text-gray-400 mb-6 text-center max-w-md">No hemos podido encontrar un
                                            reproductor directo, pero puedes verlo en la fuente oficial.</p>
                                        <a href="{{ $trailerUrl }}" target="_blank" rel="noreferrer"
                                            class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white py-3 px-8 rounded-xl transition-all shadow-lg font-bold">
                                            <i class="fas fa-external-link-alt mr-2"></i>Ver Tráiler Externo
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mb-8">
                            <span
                                class="text-xs font-bold uppercase text-blue-400 bg-blue-900/30 px-3 py-1 rounded-lg mb-3 inline-block">{{ data_get($details, 'source') }}</span>
                            <h1 class="text-4xl font-extrabold text-white mb-2">{{ data_get($details, 'title') }}</h1>
                            @if(data_get($details, 'original_title') && data_get($details, 'original_title') !== data_get($details, 'title'))
                                <p class="text-gray-400 text-xl">{{ data_get($details, 'original_title') }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-400">{{ data_get($details, 'type') }}</div>
                                <div class="text-sm text-gray-400">Tipo</div>
                            </div>
                            @if(data_get($details, 'year'))
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-400">{{ data_get($details, 'year') }}</div>
                                    <div class="text-sm text-gray-400">Año</div>
                                </div>
                            @endif
                            @if(!empty(data_get($details, 'episodes')))
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-400">{{ data_get($details, 'episodes') }}</div>
                                    <div class="text-sm text-gray-400">Episodios</div>
                                </div>
                            @elseif(!empty(data_get($details, 'chapters')))
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-400">{{ data_get($details, 'chapters') }}</div>
                                    <div class="text-sm text-gray-400">Capítulos</div>
                                </div>
                            @endif
                        </div>

                        @if(!empty(data_get($details, 'genres')) && count(data_get($details, 'genres')) > 0)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-100 mb-2">Géneros</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(data_get($details, 'genres') as $genre)
                                        <span
                                            class="bg-gray-700 text-gray-300 px-3 py-1 rounded-full text-sm">{{ $genre }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif



                        @if(!empty(data_get($details, 'synopsis')))
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-100 mb-2">Sinopsis</h3>
                                <p class="text-gray-300 leading-relaxed">{{ data_get($details, 'synopsis') }}</p>
                            </div>
                        @endif





                        @if(!empty(data_get($details, 'studios')) && count(data_get($details, 'studios')) > 0)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-100 mb-2">Estudio/Productor</h3>
                                <p class="text-gray-300">{{ implode(', ', data_get($details, 'studios')) }}</p>
                            </div>
                        @endif

                        @if(!empty(data_get($details, 'authors')) && count(data_get($details, 'authors')) > 0)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-100 mb-2">Autor</h3>
                                <p class="text-gray-300">{{ implode(', ', data_get($details, 'authors')) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
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
                                <label class="block text-sm font-medium text-gray-300 mb-2">Puntuación
                                    (opcional)</label>
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
                                <label class="block text-sm font-medium text-gray-300 mb-2">Progreso (capítulos/páginas
                                    vistos)</label>
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
