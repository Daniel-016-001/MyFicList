<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $media->title }} - {{ config('app.name', 'MyFicList') }}</title>
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
                grid-template-columns: 1fr;
                grid-row-gap: 30px;
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
            if (preg_match('/(?:v=|\/embed\/|youtu\.be\/)([A-Za-z0-9_-]{11})/', $trailerUrl, $matches)) {
                $youtubeId = $matches[1];
            }
        }
    @endphp

    <main class="flex-grow pb-20">
        <!-- Contenedor con Margen Superior Forzado -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 content-container">

            <!-- Grid con Separación Lateral Forzada -->
            <div class="main-grid">

                <!-- Left Column: Poster -->
                <div>
                    <div style="position: sticky; top: 120px;">
                        <div class="rounded-2xl overflow-hidden shadow-2xl border border-white/10 bg-gray-900">
                            <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                style="width: 100%; height: auto; max-height: 500px; display: block;">
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 space-y-4">
                            @auth
                                <button onclick="document.getElementById('list-modal').classList.remove('hidden')"
                                    class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-black rounded-xl shadow-xl transition-all flex items-center justify-center gap-3">
                                    <i class="fas fa-plus"></i> AGREGAR A MI LISTA
                                </button>
                            @else
                                <a href="{{ route('login') }}"
                                    class="w-full py-4 bg-gray-800 hover:bg-gray-700 text-white font-black rounded-xl shadow-xl transition-all flex items-center justify-center gap-3">
                                    <i class="fas fa-sign-in-alt"></i> INICIA SESIÓN
                                </a>
                            @endauth

                            <div class="flex gap-4">
                                <a href="/"
                                    class="flex-1 py-3 bg-gray-900 border border-white/5 text-gray-400 text-center rounded-xl font-bold text-sm">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                                <form action="{{ route('media.add-from-search') }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="external_id" value="{{ $media->external_id }}">
                                    <input type="hidden" name="source" value="{{ $media->source }}">
                                    <input type="hidden" name="media_type" value="{{ $media->media_type }}">
                                    <button type="submit"
                                        class="w-full py-3 bg-gray-900 border border-white/5 text-gray-400 text-center rounded-xl font-bold text-sm">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <!-- Metadata Sidebar: Light Values -->
                        <div class="mt-12 space-y-4">
                            @if(!empty($extra['genres']))
                                <div class="relative group p-6 rounded-3xl transition-all duration-500 hover:bg-white/[0.03] hover:translate-x-2">
                                    <div class="mb-4">
                                        <h4 class="text-[10px] font-black text-blue-500/60 uppercase tracking-[0.4em] group-hover:text-blue-400 transition-colors duration-500">Géneros</h4>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($extra['genres'] as $genre)
                                            <span class="px-3 py-1.5 bg-blue-500/5 text-gray-400 rounded-xl text-[11px] font-medium border border-white/5 group-hover:border-blue-500/30 transition-all duration-500">
                                                {{ $genre }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($extra['platforms']))
                                <div class="relative group p-6 rounded-3xl transition-all duration-500 hover:bg-white/[0.03] hover:translate-x-2">
                                    <div class="mb-4">
                                        <h4 class="text-[10px] font-black text-purple-500/60 uppercase tracking-[0.4em] group-hover:text-purple-400 transition-colors duration-500">Plataformas</h4>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($extra['platforms'] as $platform)
                                            <span class="px-3 py-1.5 bg-purple-500/10 text-purple-300 rounded-xl text-[11px] font-medium border border-purple-500/20 group-hover:bg-purple-500/20 transition-all duration-500">
                                                {{ $platform }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($extra['studios']))
                                <div class="relative group p-6 rounded-3xl transition-all duration-500 hover:bg-white/[0.03] hover:translate-x-2">
                                    <div class="mb-3">
                                        <h4 class="text-[10px] font-black text-green-500/60 uppercase tracking-[0.4em] group-hover:text-green-400 transition-colors duration-500">Estudios</h4>
                                    </div>
                                    <div class="text-sm text-gray-400 font-medium leading-relaxed">
                                        {{ is_array($extra['studios']) ? implode(', ', $extra['studios']) : $extra['studios'] }}
                                    </div>
                                </div>
                            @endif

                            @if(!empty($extra['authors']))
                                <div class="relative group p-6 rounded-3xl transition-all duration-500 hover:bg-white/[0.03] hover:translate-x-2">
                                    <div class="mb-3">
                                        <h4 class="text-[10px] font-black text-yellow-500/60 uppercase tracking-[0.4em] group-hover:text-yellow-400 transition-colors duration-500">Creadores</h4>
                                    </div>
                                    <div class="text-sm text-gray-400 font-medium leading-relaxed">
                                        {{ is_array($extra['authors']) ? implode(', ', $extra['authors']) : $extra['authors'] }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Content -->
                <div>
                    <!-- Title & Badges -->
                    <div class="mb-10">
                        <div class="flex flex-wrap gap-3 mb-6">
                            <span
                                class="px-3 py-1 bg-blue-600/20 text-blue-400 text-xs font-black uppercase rounded-lg border border-blue-600/30">
                                {{ $media->source }}
                            </span>
                            <span
                                class="px-3 py-1 bg-purple-600/20 text-purple-400 text-xs font-black uppercase rounded-lg border border-purple-600/30">
                                {{ ucfirst($media->media_type) }}
                            </span>
                            @if(!empty($extra['year']))
                                <span class="px-3 py-1 bg-gray-800 text-gray-400 text-xs font-black rounded-lg">
                                    {{ $extra['year'] }}
                                </span>
                            @endif
                        </div>
                        <h1 class="text-5xl md:text-7xl font-black mb-6 tracking-tighter">{{ $media->title }}</h1>

                        <div class="inline-flex items-center gap-4 bg-purple-600/10 border border-purple-500/20 px-6 py-4 rounded-3xl group hover:bg-purple-600/20 transition-all duration-500">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-purple-400 uppercase tracking-widest mb-1">Puntuación MyFicList</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-4xl font-black text-white">{{ $media->average_score }}</span>
                                    <span class="text-purple-400/60 text-xl font-bold">/ 10</span>
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-[0_0_20px_rgba(147,51,234,0.4)]">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>



                    <!-- Synopsis -->
                    <div class="mb-12">
                        <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-6">Sinopsis</h3>
                        <div class="glass p-8 rounded-3xl text-xl text-gray-300 leading-relaxed synopsis-content">
                            @php
                                $synopsis = $media->synopsis ?: 'No hay descripción disponible.';
                                // Escapamos por seguridad pero permitimos nuestros cambios
                                $synopsis = e($synopsis);
                                // Convertimos los ### en encabezados estilizados
                                $synopsis = preg_replace('/###\s*(.*?)(?:\n|$)/', '<h4 class="text-blue-400 font-black mt-8 mb-4 uppercase text-sm tracking-[0.2em]">$1</h4>', $synopsis);
                                // Respetamos saltos de línea
                                $synopsis = nl2br($synopsis);
                            @endphp
                            {!! $synopsis !!}
                        </div>
                    </div>


                    <!-- Trailer Section (Moved) -->
                    @if($trailerUrl)
                        <div class="section-spacing">
                            <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-6">Multimedia</h3>
                            <div
                                class="aspect-video rounded-3xl overflow-hidden border border-white/10 bg-black shadow-2xl">
                                @if($youtubeId)
                                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $youtubeId }}?rel=0"
                                        frameborder="0" allowfullscreen></iframe>
                                @else
                                    <video class="w-full h-full" controls>
                                        <source src="{{ $trailerUrl }}" type="video/mp4">
                                    </video>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Comments Section -->
                    <div class="mt-32">
                        <h3 class="text-4xl font-black mb-12">Comunidad</h3>
                        @auth
                            <div class="glass p-8 rounded-3xl mb-12">
                                <form action="{{ route('media.comments.store', $media->id) }}" method="POST">
                                    @csrf
                                    <textarea name="content" required placeholder="¿Qué opinas de este título?"
                                        class="w-full bg-gray-800 border-none rounded-2xl p-5 text-white mb-6"
                                        rows="4"></textarea>
                                    <button type="submit"
                                        class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-bold text-sm transition-all hover:scale-[1.02] shadow-lg">
                                        Publicar Comentario
                                    </button>
                                </form>
                            </div>
                        @endauth

                        <div class="space-y-8">
                            @forelse($media->comments()->with('user')->latest()->get() as $comment)
                                <div class="glass p-8 rounded-3xl">
                                    <div class="flex justify-between items-center mb-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center font-black text-xl">
                                                {{ substr($comment->user->name, 0, 1) }}
                                            </div>
                                            <span
                                                class="font-black text-purple-400 text-lg">{{ $comment->user->name }}</span>
                                        </div>
                                        <span
                                            class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-300 text-lg">{{ $comment->content }}</p>
                                </div>
                            @empty
                                <div class="text-center py-12 text-gray-600 italic text-lg">No hay comentarios todavía.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
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
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Estado</label>
                    <select name="status" class="w-full bg-gray-800 border-none rounded-xl p-4 text-white font-bold focus:ring-2 focus:ring-purple-600">
                        <option value="watching">Viendo / Jugando</option>
                        <option value="completed">Completado</option>
                        <option value="on_hold">En Pausa</option>
                        <option value="dropped">Abandonado</option>
                        <option value="plan_to_watch">Pendiente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Puntuación (1-10)</label>
                    <select name="score" class="w-full bg-gray-800 border-none rounded-xl p-4 text-white font-bold focus:ring-2 focus:ring-purple-600">
                        <option value="">Sin nota</option>
                        @for($i = 10; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ $i }} - {{ $i == 10 ? 'Obra Maestra' : ($i >= 8 ? 'Muy Bueno' : ($i >= 5 ? 'Aceptable' : 'Pobre')) }}</option>
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
</body>

</html>