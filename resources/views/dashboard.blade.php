<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fiction top</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-950 text-gray-100 min-h-screen flex flex-col">
    @include('layouts.navigation')

    <main class="flex-grow bg-gray-950 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div
                class="mb-12 bg-gradient-to-r from-blue-900/20 to-purple-900/20 rounded-xl p-8">
                <p class="text-gray-400">Contenido mejor valorado por la comunidad</p>
            </div>

            <!-- Anime -->
            @if(isset($popularByCategory['anime']) && $popularByCategory['anime']->count() > 0)
                <section class="mb-20">
                    <h3 class="text-xs font-black text-blue-500 uppercase tracking-[0.4em] mb-10 flex items-center gap-6">
                        Anime Mas Popular
                        <span class="h-px flex-grow bg-gradient-to-r from-blue-500/20 to-transparent"></span>
                    </h3>
                    <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-6">
                        @foreach($popularByCategory['anime'] as $media)
                            <div class="break-inside-avoid mb-6">
                                <div
                                    class="bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.02] transform-gpu flex flex-col">
                                    <a href="{{ route('media.show', $media->id) }}" class="block relative group cursor-pointer">
                                        <div class="relative">
                                            <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                                class="w-full h-auto object-cover brightness-90 group-hover:brightness-100 transition-all rounded-t-2xl">
                                            @if($media->average_score !== 'N/A')
                                                <div style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.75); border-radius: 0.5rem; padding: 0.25rem 0.5rem; z-index: 10; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;"
                                                    class="backdrop-blur-sm shadow-lg text-yellow-400 text-xs font-black">
                                                    <i class="fas fa-star text-[9px]"></i> {{ $media->average_score }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="p-5 flex-grow flex flex-col">
                                        <h4 class="font-bold text-lg text-white leading-tight mb-0">{{ $media->title }}</h4>
                                        <div class="mt-auto flex flex-wrap items-center justify-between gap-2 pt-4 border-slate-800/50">
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
                </section>
            @endif

            <!-- Manga -->
            @if(isset($popularByCategory['manga']) && $popularByCategory['manga']->count() > 0)
                <section class="mb-20">
                    <h3 class="text-xs font-black text-purple-500 uppercase tracking-[0.4em] mb-10 flex items-center gap-6">
                        Manga Destacado
                        <span class="h-px flex-grow bg-gradient-to-r from-purple-500/20 to-transparent"></span>
                    </h3>
                    <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-6">
                        @foreach($popularByCategory['manga'] as $media)
                            <div class="break-inside-avoid mb-6">
                                <div
                                    class="bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.02] transform-gpu flex flex-col">
                                    <a href="{{ route('media.show', $media->id) }}" class="block relative group cursor-pointer">
                                        <div class="relative">
                                            <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                                class="w-full h-auto object-cover brightness-90 group-hover:brightness-100 transition-all rounded-t-2xl">
                                            @if($media->average_score !== 'N/A')
                                                <div style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.75); border-radius: 0.5rem; padding: 0.25rem 0.5rem; z-index: 10; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;"
                                                    class="backdrop-blur-sm shadow-lg text-yellow-400 text-xs font-black">
                                                    <i class="fas fa-star text-[9px]"></i> {{ $media->average_score }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="p-5 flex-grow flex flex-col">
                                        <h4 class="font-bold text-lg text-white leading-tight mb-0">{{ $media->title }}</h4>
                                        <div class="mt-auto flex flex-wrap items-center justify-between gap-2 pt-4 border-slate-800/50">
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
                </section>
            @endif

            <!-- Películas -->
            @if(isset($popularByCategory['peli']) && $popularByCategory['peli']->count() > 0)
                <section class="mb-20">
                    <h3 class="text-xs font-black text-red-500 uppercase tracking-[0.4em] mb-10 flex items-center gap-6">
                        Cine y Películas
                        <span class="h-px flex-grow bg-gradient-to-r from-red-500/20 to-transparent"></span>
                    </h3>
                    <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-6">
                        @foreach($popularByCategory['peli'] as $media)
                            <div class="break-inside-avoid mb-6">
                                <div
                                    class="bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.02] transform-gpu flex flex-col">
                                    <a href="{{ route('media.show', $media->id) }}" class="block relative group cursor-pointer">
                                        <div class="relative">
                                            <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                                class="w-full h-auto object-cover brightness-90 group-hover:brightness-100 transition-all rounded-t-2xl">
                                            @if($media->average_score !== 'N/A')
                                                <div style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.75); border-radius: 0.5rem; padding: 0.25rem 0.5rem; z-index: 10; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;"
                                                    class="backdrop-blur-sm shadow-lg text-yellow-400 text-xs font-black">
                                                    <i class="fas fa-star text-[9px]"></i> {{ $media->average_score }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="p-5 flex-grow flex flex-col">
                                        <h4 class="font-bold text-lg text-white leading-tight mb-0">{{ $media->title }}</h4>
                                        <div class="mt-auto flex flex-wrap items-center justify-between gap-2 pt-4 border-slate-800/50">
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
                </section>
            @endif

            <!-- Series -->
            @if(isset($popularByCategory['serie']) && $popularByCategory['serie']->count() > 0)
                <section class="mb-20">
                    <h3 class="text-xs font-black text-green-500 uppercase tracking-[0.4em] mb-10 flex items-center gap-6">
                        Series de TV
                        <span class="h-px flex-grow bg-gradient-to-r from-green-500/20 to-transparent"></span>
                    </h3>
                    <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-6">
                        @foreach($popularByCategory['serie'] as $media)
                            <div class="break-inside-avoid mb-6">
                                <div
                                    class="bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.02] transform-gpu flex flex-col">
                                    <a href="{{ route('media.show', $media->id) }}" class="block relative group cursor-pointer">
                                        <div class="relative">
                                            <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                                class="w-full h-auto object-cover brightness-90 group-hover:brightness-100 transition-all rounded-t-2xl">
                                            @if($media->average_score !== 'N/A')
                                                <div style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.75); border-radius: 0.5rem; padding: 0.25rem 0.5rem; z-index: 10; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;"
                                                    class="backdrop-blur-sm shadow-lg text-yellow-400 text-xs font-black">
                                                    <i class="fas fa-star text-[9px]"></i> {{ $media->average_score }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="p-5 flex-grow flex flex-col">
                                        <h4 class="font-bold text-lg text-white leading-tight mb-0">{{ $media->title }}</h4>
                                        <div class="mt-auto flex flex-wrap items-center justify-between gap-2 pt-4 border-slate-800/50">
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
                </section>
            @endif

            <!-- Videojuegos -->
            @if(isset($popularByCategory['game']) && $popularByCategory['game']->count() > 0)
                <section class="mb-20">
                    <h3 class="text-xs font-black text-yellow-500 uppercase tracking-[0.4em] mb-10 flex items-center gap-6">
                        Videojuegos Top
                        <span class="h-px flex-grow bg-gradient-to-r from-yellow-500/20 to-transparent"></span>
                    </h3>
                    <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-6">
                        @foreach($popularByCategory['game'] as $media)
                            @php
                                $platforms = $media->extra_data['platforms'] ?? [];
                                $platformIcons = [];
                                foreach ($platforms as $p) {
                                    $pLower = strtolower($p);
                                    if (str_contains($pLower, 'pc') || str_contains($pLower, 'windows'))
                                        $platformIcons[] = 'fab fa-windows';
                                    if (str_contains($pLower, 'playstation') || str_contains($pLower, 'ps'))
                                        $platformIcons[] = 'fab fa-playstation';
                                    if (str_contains($pLower, 'xbox'))
                                        $platformIcons[] = 'fab fa-xbox';
                                }
                                $platformIcons = array_unique($platformIcons);
                            @endphp
                            <div class="break-inside-avoid mb-6">
                                <div
                                    class="bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.02] transform-gpu flex flex-col">
                                    <a href="{{ route('media.show', $media->id) }}" class="block relative group cursor-pointer">
                                        <div class="relative">
                                            <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                                class="w-full h-auto object-cover brightness-90 group-hover:brightness-100 transition-all rounded-t-2xl">
                                            @if($media->average_score !== 'N/A')
                                                <div style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.75); border-radius: 0.5rem; padding: 0.25rem 0.5rem; z-index: 10; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;"
                                                    class="backdrop-blur-sm shadow-lg text-yellow-400 text-xs font-black">
                                                    <i class="fas fa-star text-[9px]"></i> {{ $media->average_score }}
                                                </div>
                                            @endif

                                        </div>
                                    </a>
                                    <div class="p-5 flex-grow flex flex-col">
                                        @if(!empty($platformIcons))
                                            <!-- Platforms -->
                                            <div class="flex items-center gap-2 mb-3">
                                                @foreach($platformIcons as $icon)
                                                    <i class="{{ $icon }} text-blue-400 text-[11px]"></i>
                                                @endforeach
                                            </div>
                                        @endif
                                        <h4 class="font-bold text-lg text-white leading-tight mb-0">{{ $media->title }}</h4>
                                        <div class="mt-auto flex flex-wrap items-center justify-between gap-2 pt-4 border-slate-800/50">
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
                </section>
            @endif

            <!-- Libros -->
            @if(isset($popularByCategory['book']) && $popularByCategory['book']->count() > 0)
                <section class="mb-20">
                    <h3 class="text-xs font-black text-orange-500 uppercase tracking-[0.4em] mb-10 flex items-center gap-6">
                        Libros y Novelas
                        <span class="h-px flex-grow bg-gradient-to-r from-orange-500/20 to-transparent"></span>
                    </h3>
                    <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-6">
                        @foreach($popularByCategory['book'] as $media)
                            <div class="break-inside-avoid mb-6">
                                <div
                                    class="bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.02] transform-gpu flex flex-col">
                                    <a href="{{ route('media.show', $media->id) }}" class="block relative group cursor-pointer">
                                        <div class="relative">
                                            <img src="{{ $media->cover_url }}" alt="{{ $media->title }}"
                                                class="w-full h-auto object-cover brightness-90 group-hover:brightness-100 transition-all rounded-t-2xl">
                                            @if($media->average_score !== 'N/A')
                                                <div style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.75); border-radius: 0.5rem; padding: 0.25rem 0.5rem; z-index: 10; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;"
                                                    class="backdrop-blur-sm shadow-lg text-yellow-400 text-xs font-black">
                                                    <i class="fas fa-star text-[9px]"></i> {{ $media->average_score }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="p-5 flex-grow flex flex-col">
                                        <h4 class="font-bold text-lg text-white leading-tight mb-0">{{ $media->title }}</h4>
                                        <div class="mt-auto flex flex-wrap items-center justify-between gap-2 pt-2 border-slate-800/50">
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
                </section>
            @endif

            <!-- Sin contenido valorado -->
            @if(empty($popularByCategory) || collect($popularByCategory)->flatten()->isEmpty())
                <div class="text-center py-20">
                    <p class="text-gray-400 mb-4">No hay contenidos valorados aún.</p>
                    <a href="{{ route('media.explore') }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded-lg">Explorar
                        Catálogo</a>
                </div>
            @endif
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
                <input type="hidden" name="media_id" id="modal-media-id" value="">
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Lista</label>
                    <select name="media_list_id" id="media-list-select"
                        class="w-full bg-gray-800 border-none rounded-xl p-4 text-white font-bold focus:ring-2 focus:ring-purple-600">
                        <option value="">Mi Lista (Predeterminada)</option>
                        @auth
                            @foreach(Auth::user()->mediaLists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }}
                                    {{ $list->is_public ? '(Pública)' : '(Privada)' }}
                                </option>
                            @endforeach
                        @endauth
                        <option value="new">+ Crear nueva lista</option>
                    </select>
                </div>
                <div id="new-list-fields" class="hidden space-y-4">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Nombre de
                            la lista</label>
                        <input type="text" name="new_list_name"
                            class="w-full bg-gray-800 border-none rounded-xl p-4 text-white font-bold focus:ring-2 focus:ring-purple-600"
                            placeholder="Ej: Favoritos, Ver más tarde...">
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_public" id="is-public"
                            class="w-4 h-4 text-purple-600 bg-gray-800 border-gray-600 rounded focus:ring-purple-600">
                        <label for="is-public" class="text-sm font-bold text-gray-300">Lista pública (visible para otros
                            usuarios)</label>
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
        function openListModal(mediaId) {
            document.getElementById('modal-media-id').value = mediaId;
            document.getElementById('list-modal').classList.remove('hidden');
        }

        document.getElementById('media-list-select').addEventListener('change', function () {
            const newListFields = document.getElementById('new-list-fields');
            if (this.value === 'new') {
                newListFields.classList.remove('hidden');
            } else {
                newListFields.classList.add('hidden');
            }
        });
    </script>
</body>

</html>