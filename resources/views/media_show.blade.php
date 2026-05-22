<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $media->title }}</title>
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

        .content-container {
            padding-top: 80px !important;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 3fr;
            grid-column-gap: 60px !important;
        }

        @media (max-width: 1024px) {
            .main-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 30px !important;
            }

            .left-column,
            .right-column,
            .sticky-container {
                display: contents !important;
            }

            .poster-actions-section {
                order: 1;
            }

            .title-section {
                order: 2;
            }

            .categories-section {
                order: 3;
            }

            .synopsis-section {
                order: 4;
            }

            .trailer-section {
                order: 5;
            }

            .comments-section {
                order: 6;
            }
        }

        .section-spacing {
            margin-bottom: 80px !important;
        }

        .synopsis-content h4 {
            color: #60a5fa;
            font-weight: 800;
            margin-top: 40px;
            margin-bottom: 16px;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.1em;
        }
    </style>
</head>

<body class="bg-gray-950 text-gray-100 min-h-screen flex flex-col">
    @include('layouts.navigation')

    @php
        $extra = $media->extra_data ?? [];
        $trailerUrl = $extra['trailer_url'] ?? null;
        $isYoutube = !empty($trailerUrl) && (str_contains($trailerUrl, 'youtube.com') || str_contains($trailerUrl, 'youtu.be'));
        $youtubeId = null;
        if ($isYoutube) {
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $trailerUrl, $matches)) {
                $youtubeId = $matches[1];
            }
        }
        // Detectar tipo MIME por extensión para el <video> tag
        $videoMime = 'video/mp4'; // Por defecto mp4
        if ($trailerUrl && str_ends_with(strtolower(parse_url($trailerUrl, PHP_URL_PATH) ?? ''), '.webm')) {
            $videoMime = 'video/webm';
        } elseif ($trailerUrl && str_ends_with(strtolower(parse_url($trailerUrl, PHP_URL_PATH) ?? ''), '.ogg')) {
            $videoMime = 'video/ogg';
        }
    @endphp

    <main class="flex-grow pb-20">
        <!-- Contenedor con Margen Superior Forzado -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 content-container">

            <!-- Grid con Separación Lateral Forzada -->
            <div class="main-grid">

                <!-- Left Column: Poster -->
                <div class="space-y-10 left-column">
                    <div class="sticky-container" style="position: sticky; top: 120px;">
                        <!-- Poster and Actions Wrapper -->
                        <div class="poster-actions-section">
                            <div class="relative group">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-[2rem] blur opacity-25 group-hover:opacity-60 transition duration-1000">
                                </div>
                                <div
                                    class="relative rounded-[2rem] overflow-hidden shadow-2xl bg-gray-950 shimmer">
                                    <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                        class="w-full h-auto transition-transform duration-700 group-hover:scale-105">
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-12 space-y-4">
                                @auth
                                    <button onclick="document.getElementById('list-modal').classList.remove('hidden')"
                                        class="w-full py-5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-black rounded-2xl shadow-xl shadow-purple-900/20 transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 uppercase tracking-tighter">
                                        <i class="fas fa-plus"></i> AGREGAR A MI LISTA
                                    </button>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="w-full py-5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-black rounded-2xl shadow-xl shadow-purple-900/20 transition-all hover:scale-[1.02] flex items-center justify-center gap-3 uppercase tracking-tighter">
                                        <i class="fas fa-sign-in-alt"></i> INICIA SESIÓN
                                    </a>
                                @endauth

                                <div class="flex gap-4">
                                    <a href="/"
                                        class="flex-1 py-4 bg-gray-900/50 text-gray-500 text-center rounded-2xl font-black text-xs hover:text-white transition-all">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                    <form action="{{ route('media.add-from-search') }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="external_id" value="{{ $media->external_id }}">
                                        <input type="hidden" name="source" value="{{ $media->source }}">
                                        <input type="hidden" name="media_type" value="{{ $media->media_type }}">
                                        <button type="submit"
                                            class="w-full py-4 bg-gray-900/50 text-gray-500 text-center rounded-2xl font-black text-xs hover:text-white transition-all">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Metadata Sidebar -->
                        @php
                            $hasFichaTecnica = $media->episodes_count || $media->episode_duration || ($media->total_duration && $media->media_type === 'game') || $media->chapters || !empty($extra['number_of_seasons']);
                            $hasTechnicalData = $hasFichaTecnica || !empty($extra['genres']) || !empty($extra['categories']) || !empty($extra['platforms']) || !empty($extra['studios']) || !empty($extra['authors']);
                        @endphp
                        
                        @if($hasTechnicalData)
                        <div class="mt-12 space-y-10 categories-section">
                            <!-- Ficha Técnica -->
                            @if($hasFichaTecnica)
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">Ficha
                                    Técnica</h4>
                                <div class="grid grid-cols-1 gap-3">
                                    @if($media->episodes_count)
                                        <div
                                            class="flex items-center gap-4 bg-gray-900/30 p-4 rounded-2xl group transition-colors">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                                                <i class="fas fa-layer-group text-xs"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-0.5">
                                                    Episodios</p>
                                                <p class="text-xs font-bold text-gray-200">{{ $media->episodes_count }}
                                                    capítulos</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($media->episode_duration)
                                        <div
                                            class="flex items-center gap-4 bg-gray-900/30 p-4 rounded-2xl group transition-colors">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                                                <i class="fas fa-clock text-xs"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-0.5">
                                                    Duración</p>
                                                <p class="text-xs font-bold text-gray-200">{{ $media->episode_duration }}
                                                    @if($media->media_type !== 'peli')@endif</p>
                                            </div>
                                        </div>
                                    @endif



                                    @if($media->total_duration && $media->media_type === 'game')
                                        <div
                                            class="flex items-center gap-4 bg-gray-900/30 p-4 rounded-2xl group transition-colors">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center text-green-400">
                                                <i class="fas fa-gamepad text-xs"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-0.5">
                                                    Promedio de Juego</p>
                                                <p class="text-xs font-bold text-gray-200">{{ $media->total_duration }} horas
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($media->chapters)
                                        <div
                                            class="flex items-center gap-4 bg-gray-900/30 p-4 rounded-2xl group transition-colors">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                                                <i class="fas fa-book-open text-xs"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-0.5">
                                                    Capítulos</p>
                                                <p class="text-xs font-bold text-gray-200">{{ $media->chapters }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if(!empty($extra['number_of_seasons']))
                                        <div
                                            class="flex items-center gap-4 bg-gray-900/30 p-4 rounded-2xl group transition-colors">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-400">
                                                <i class="fas fa-layer-group text-xs"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-0.5">
                                                    Temporadas</p>
                                                <p class="text-xs font-bold text-gray-200">{{ $extra['number_of_seasons'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if(!empty($extra['genres']))
                                <div class="space-y-4">
                                    <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-[0.3em]">Géneros</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($extra['genres'] as $genre)
                                            <a href="{{ route('media.explore', ['genre' => $genre]) }}"
                                                style="background-color: rgba(37, 99, 235, 0.1) !important; color: #60a5fa !important;"
                                                class="rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-tight hover:bg-blue-900/40 hover:scale-105 transition-all inline-block cursor-pointer">
                                                {{ $genre }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($extra['categories']))
                                <div class="space-y-4">
                                    <h4 class="text-[10px] font-black text-teal-500 uppercase tracking-[0.3em]">Categorías / Etiquetas</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach((array) $extra['categories'] as $category)
                                            <span
                                                style="background-color: rgba(20, 184, 166, 0.1) !important; color: #2dd4bf !important;"
                                                class="rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-tight">
                                                {{ is_array($category) ? ($category['name'] ?? '') : $category }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($extra['platforms']))
                                <div class="space-y-4">
                                    <h4 class="text-[10px] font-black text-purple-500 uppercase tracking-[0.3em]">
                                        Plataformas</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($extra['platforms'] as $platform)
                                            <a href="{{ route('media.explore', ['type' => 'game', 'platform' => $platform]) }}"
                                                style="background-color: rgba(147, 51, 234, 0.1) !important; color: #c084fc !important;"
                                                class="rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-tight hover:bg-purple-900/40 hover:scale-105 transition-all inline-block cursor-pointer">
                                                {{ $platform }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($extra['studios']))
                                <div class="space-y-4">
                                    <h4 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em]">Estudios
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach((array) $extra['studios'] as $studio)
                                            <span
                                                style="background-color: rgba(5, 150, 105, 0.1) !important; color: #34d399 !important;"
                                                class="rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-tight">
                                                {{ is_array($studio) ? ($studio['name'] ?? '') : $studio }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($extra['authors']))
                                <div class="space-y-4">
                                    <h4 class="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em]">Autores
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach((array) $extra['authors'] as $author)
                                            <span
                                                style="background-color: rgba(249, 115, 22, 0.1) !important; color: #fb923c !important;"
                                                class="rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-tight">
                                                {{ is_array($author) ? ($author['name'] ?? '') : $author }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Content -->
                <div class="space-y-20 right-column">
                    <!-- Title & Badges -->
                    <div class="space-y-6 title-section">
                        <div class="flex flex-wrap gap-2">
                            <span
                                class="px-3 py-1 bg-purple-600/10 text-purple-400 rounded-full text-[8px] font-black uppercase tracking-widest">{{ $media->source }}</span>
                            <span
                                class="px-3 py-1 bg-purple-600/10 text-purple-400 rounded-full text-[8px] font-black uppercase tracking-widest">{{ $media->media_type }}</span>
                            @if(!empty($extra['year']))
                                <span
                                    class="px-3 py-1 bg-gray-800/40 text-gray-400 rounded-full text-[8px] font-black uppercase tracking-widest">{{ $extra['year'] }}</span>
                            @endif
                        </div>

                        <h1 class="text-4xl font-black text-white tracking-tighter uppercase leading-tight">
                            {{ $media->title }}
                        </h1>

                        <div
                            class="inline-flex items-center gap-4 bg-gray-900/20 p-4 rounded-2xl">
                            <div>
                                <p class="text-[7px] font-black text-purple-500 uppercase tracking-widest mb-0.5">
                                    PUNTUACIÓN MYFICLIST</p>
                                <div class="flex items-baseline gap-1">
                                    <span
                                        class="text-3xl font-black text-white leading-none">{{ $media->average_score ?: 'N/A' }}</span>
                                    <span class="text-gray-500 font-bold text-[10px]">/10</span>
                                </div>
                            </div>
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-star text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Synopsis -->
                    <section class="pt-8 space-y-6 synopsis-section">
                        <h3 class="text-xs font-black text-gray-500 uppercase tracking-[0.4em]">Sinopsis</h3>
                        <div class="bg-gray-900/20 rounded-3xl p-8">
                            <div class="text-gray-400 text-sm leading-relaxed">
                                @php
                                    $synopsis = $media->synopsis;
                                    if (empty($synopsis) || $synopsis === '...') {
                                        $synopsis = 'No hay descripción disponible para este título.';
                                    }
                                @endphp
                                {!! nl2br(e($synopsis)) !!}
                            </div>
                        </div>
                    </section>


                    <!-- Trailer Section -->
                    @if($trailerUrl)
                        <section class="pt-8 space-y-6 trailer-section">
                            <h3 class="text-xs font-black text-gray-500 uppercase tracking-[0.4em]">Multimedia</h3>
                            <div
                                class="aspect-video rounded-3xl overflow-hidden bg-black shadow-2xl">
                                @if($youtubeId)
                                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $youtubeId }}?rel=0"
                                        frameborder="0" allowfullscreen></iframe>
                                @else
                                    <video class="w-full h-full" controls preload="metadata">
                                        <source src="{{ $trailerUrl }}" type="{{ $videoMime }}">
                                        Tu navegador no soporta la reproducción de vídeo.
                                    </video>
                                @endif
                            </div>
                        </section>
                    @endif

                    <!-- Comments Section -->
                    <div class="comments-section">
                        <x-comments :model="$media" />
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        async function toggleLike(id, type, button) {
            @guest
                window.location.href = "{{ route('login') }}";
                return;
            @endguest

        try {
                const response = await fetch("{{ route('like.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        likeable_id: id,
                        likeable_type: type
                    })
                });

                const data = await response.json();

                if (data.status === 'liked') {
                    button.classList.add('text-red-500', 'bg-red-500/10');
                    button.classList.remove('text-gray-400', 'bg-gray-800/50');
                    button.querySelector('i').classList.replace('far', 'fas');
                } else {
                    button.classList.remove('text-red-500', 'bg-red-500/10');
                    button.classList.add('text-gray-400', 'bg-gray-800/50');
                    button.querySelector('i').classList.replace('fas', 'far');
                }

                button.querySelector('.like-count').textContent = data.count;
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        }
    </script>
    @include('layouts.footer')

    <!-- Modal -->
    <div id="list-modal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md hidden">
        <div class="glass max-w-md w-full p-10 rounded-3xl">
            <h3 class="text-3xl font-black mb-8">Agregar a mi lista</h3>
            <form action="{{ route('user-list.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="media_id" value="{{ $media->id }}">

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Lista</label>
                    <select name="media_list_id" onchange="toggleNewListForm(this.value)"
                        class="w-full bg-gray-800 border-none rounded-xl p-4 text-white font-bold focus:ring-2 focus:ring-purple-600">
                        @auth
                            @php
                                $userLists = \App\Models\MediaList::where('user_id', auth()->id())->get();
                            @endphp
                            @foreach($userLists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }}</option>
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
        function toggleNewListForm(value) {
            const fields = document.getElementById('new-list-fields');
            if (value === 'new') {
                fields.classList.remove('hidden');
            } else {
                fields.classList.add('hidden');
            }
        }
    </script>
</body>

</html>