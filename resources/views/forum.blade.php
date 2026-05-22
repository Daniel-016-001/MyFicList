@extends('layouts.app')
@section('title', 'Foro de la Comunidad')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pb-20">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-bold text-white tracking-tight">Foro de la comunidad</h1>
                <p class="mt-2 text-gray-400 max-w-2xl">Lee y comparte lo que estás viendo, tu lista de favoritos y tus
                    recomendaciones con el resto de la comunidad.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-10 rounded-2xl bg-green-500/10 border border-green-500/20 p-4 text-green-400 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-8 space-y-10">

                @auth
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-white uppercase tracking-wider">Publicaciones</h2>
                        <button id="toggle-forum-form"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 text-xs md:px-6 md:py-2 md:text-sm rounded-xl transition shadow-lg shadow-blue-900/20 whitespace-nowrap shrink-0">
                            <span class="hidden md:inline">NUEVA PUBLICACIÓN</span>
                            <span class="md:hidden"><i class="fas fa-plus mr-1"></i>Publicar</span>
                        </button>
                    </div>

                    <div id="forum-form-panel"
                        class="bg-gray-900/50 rounded-3xl p-8 border border-white/5 mb-10 {{ old('title') || old('body') || old('media_title') || old('media_id') ? '' : 'hidden' }}">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-xl font-bold text-white">¿Qué quieres compartir?</h2>
                            <button id="close-forum-form" class="text-gray-500 hover:text-white transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <form action="{{ route('forum.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Categoría</label>
                                    <select name="category"
                                        class="w-full bg-gray-950 border border-white/10 rounded-xl p-3 text-white focus:ring-2 focus:ring-blue-600">
                                        @foreach($categories as $key => $label)
                                            <option value="{{ $key }}" {{ old('category', 'general') === $key ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Título</label>
                                    <input name="title" type="text" value="{{ old('title') }}"
                                        class="w-full bg-gray-950 border border-white/10 rounded-xl p-3 text-white focus:ring-2 focus:ring-blue-600"
                                        placeholder="Título de tu post">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Contenido</label>
                                <textarea name="body" rows="4"
                                    class="w-full bg-gray-950 border border-white/10 rounded-xl p-3 text-white focus:ring-2 focus:ring-blue-600"
                                    placeholder="Escribe aquí..."></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="relative space-y-2">
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Vincular
                                        contenido</label>
                                    <input id="media_title" name="media_title" type="text" value="{{ old('media_title') }}"
                                        autocomplete="off"
                                        class="w-full bg-gray-950 border border-white/10 rounded-xl p-3 text-white focus:ring-2 focus:ring-blue-600"
                                        placeholder="Busca algo...">
                                    <input id="media_id" name="media_id" type="hidden" value="{{ old('media_id') }}">
                                    <div id="media-suggestions"
                                        class="absolute z-50 mt-1 w-full rounded-xl border border-white/10 bg-gray-950 shadow-2xl hidden max-h-64 overflow-y-auto">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Adjuntar
                                        Imagen</label>
                                    <input name="attachment" type="file"
                                        class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-white/5 file:text-white" />
                                </div>
                            </div>

                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-5 py-2.5 rounded-xl transition">
                                PUBLICAR
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-blue-900/10 border border-blue-500/10 rounded-2xl p-8 text-center mb-10">
                        <p class="text-gray-300 font-medium text-sm">Inicia sesión para compartir publicaciones con la
                            comunidad.</p>
                    </div>
                @endauth

                <!-- Filtros y Buscador -->
                <div class="flex flex-col md:flex-row gap-6 mb-10 items-center justify-between">
                    <div class="flex flex-wrap gap-3">
                        @foreach($categories as $key => $label)
                            <a href="{{ route('forum.index', ['category' => $key, 'search' => $search]) }}"
                                class="px-5 py-2 rounded-full text-sm font-medium transition-all {{ $selectedCategory === $key ? 'bg-blue-600 text-white' : 'bg-gray-900 text-gray-400 hover:text-white border border-white/5' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <form action="{{ route('forum.index') }}" method="GET" class="relative w-full md:w-64">
                        <input type="hidden" name="category" value="{{ $selectedCategory }}">
                        <input type="text" name="search" value="{{ $search }}"
                            class="w-full bg-gray-900 border border-white/5 rounded-2xl py-2 pl-10 pr-4 text-sm text-white focus:ring-2 focus:ring-blue-600"
                            placeholder="Buscar en el foro...">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-xs"></i>
                    </form>
                </div>

                <div class="space-y-6">
                    @forelse($items as $item)
                        @if($item instanceof \App\Models\MediaList)
                            <!-- Rendering MediaList -->
                            <div
                                class="bg-gray-900/40 border border-white/5 rounded-3xl p-8 hover:bg-gray-900/60 transition-all group">
                                <div class="flex justify-between items-start mb-6">
                                    <div class="flex gap-4 items-center">
                                        <img src="{{ $item->user->avatar_url }}"
                                            class="w-10 h-10 rounded-xl object-cover border border-white/5" alt="">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Lista
                                                creada por</p>
                                            <a href="{{ route('users.show', $item->user->username) }}"
                                                class="text-white font-bold hover:text-blue-400 transition-colors uppercase">{{ $item->user->username }}</a>
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold text-gray-600 uppercase">{{ $item->updated_at->diffForHumans() }}</span>
                                </div>

                                <div class="space-y-4 mb-6">
                                    <h3
                                        class="text-2xl font-black text-white group-hover:text-blue-400 transition-colors uppercase tracking-tight">
                                        {{ $item->name }}</h3>
                                    <div class="flex flex-wrap gap-2 mt-4">
                                        @foreach($item->items->take(5) as $entry)
                                            @if($entry->media)
                                                <div
                                                    class="w-12 h-16 rounded-lg overflow-hidden border border-white/5 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                                    <img src="{{ $entry->media->cover_url }}" class="w-full h-full object-cover" alt="">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-8 pt-6 border-t border-white/5">
                                    <div class="flex items-center gap-6">
                                        <button onclick="toggleLike({{ $item->id }}, 'media_list', this)"
                                            class="flex items-center gap-2 text-gray-500 hover:text-white transition-colors">
                                            <i
                                                class="{{ auth()->user() && $item->isLikedBy(auth()->user()) ? 'fas text-red-500' : 'far' }} fa-heart text-sm"></i>
                                            <span class="like-count font-bold text-sm">{{ $item->likes()->count() }}</span>
                                        </button>
                                        <a href="{{ route('media-lists.show', $item) }}"
                                            class="text-blue-400 hover:text-blue-300 font-black text-[10px] uppercase tracking-widest">Ver
                                            lista completa</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Rendering ForumPost -->
                            <article
                                class="bg-gray-900/40 border border-white/5 rounded-3xl p-8 hover:bg-gray-900/60 transition-all group">
                                <div class="flex justify-between items-start mb-6">
                                    <div class="flex gap-4 items-center">
                                        <img src="{{ $item->user->avatar_url }}"
                                            class="w-10 h-10 rounded-xl object-cover border border-white/5" alt="">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">
                                                Publicado por</p>
                                            @if($item->user->username)
                                                <a href="{{ route('users.show', $item->user->username) }}"
                                                    class="text-white font-bold hover:text-blue-400 transition-colors">{{ $item->user->username }}</a>
                                            @else
                                                <span class="text-white font-bold">{{ $item->user->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold text-gray-600 uppercase">{{ $item->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="space-y-4 mb-6">
                                    <span
                                        class="inline-block bg-white/5 text-gray-400 text-[9px] font-black uppercase px-2 py-1 rounded-md tracking-wider">
                                        {{ $item->category ? str_replace('_', ' ', $item->category) : 'GENERAL' }}
                                    </span>
                                    <h3 class="text-xl font-bold text-white group-hover:text-blue-400 transition-colors">
                                        {{ $item->title }}</h3>
                                    <p class="text-gray-400 text-sm leading-relaxed">{{ $item->body }}</p>
                                </div>

                                @if($item->media)
                                    <a href="{{ route('media.show', $item->media) }}"
                                        class="inline-flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all mb-6">
                                        <div class="w-8 h-8 rounded-lg bg-blue-600/20 flex items-center justify-center text-blue-400">
                                            <i class="fas fa-link text-xs"></i>
                                        </div>
                                        <span class="text-xs font-bold text-white">{{ $item->media->title }}</span>
                                    </a>
                                @endif

                                @if($item->attachment_path)
                                    @php
                                        $attachmentUrl = str_starts_with($item->attachment_path, 'http') 
                                            ? $item->attachment_path 
                                            : asset('storage/' . $item->attachment_path);
                                    @endphp
                                    <div class="pt-2 border-t border-white/5 mt-6">
                                        <p class="text-[10px] font-bold text-gray-600 uppercase mb-3">Archivo adjunto</p>
                                        <a href="{{ $attachmentUrl }}" target="_blank" class="block w-fit rounded-xl overflow-hidden border border-white/5 bg-black/20 shadow-2xl">
                                            <img src="{{ $attachmentUrl }}"
                                                class="max-w-full max-h-56 object-contain hover:scale-105 transition-transform duration-500"
                                                alt="Adjunto del foro">
                                        </a>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between mt-8 pt-6 border-t border-white/5">
                                    <div class="flex items-center gap-4">
                                        <button onclick="toggleLike({{ $item->id }}, 'post', this)"
                                            class="flex items-center gap-2 text-gray-500 hover:text-white transition-colors">
                                            <i
                                                class="{{ auth()->user() && $item->isLikedBy(auth()->user()) ? 'fas text-red-500' : 'far' }} fa-heart text-sm"></i>
                                            <span class="like-count font-bold text-sm">{{ $item->likes()->count() }}</span>
                                        </button>

                                        <button onclick="togglePostComments({{ $item->id }})"
                                            class="flex items-center gap-2 text-gray-500 hover:text-white transition-colors">
                                            <i class="fas fa-comment text-sm"></i>
                                            <span class="font-bold text-sm">{{ $item->comments()->count() }}</span>
                                        </button>
                                    </div>

                                    @if(auth()->check() && (auth()->id() === $item->user_id || auth()->user()->role === 'admin'))
                                        <form action="{{ route('forum.destroy', $item) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="text-gray-600 hover:text-red-500 transition-colors text-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Post Comments Section -->
                                <div id="post-comments-{{ $item->id }}" class="hidden mt-8 pt-8 border-t border-white/5">
                                    <x-comments :model="$item" />
                                </div>
                            </article>
                        @endif
                    @empty
                        <div class="p-12 text-center bg-gray-900/20 rounded-3xl border border-white/5">
                            <p class="text-gray-500 font-medium">No hay resultados para esta búsqueda o categoría.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-12">
                    {{ $items->links() }}
                </div>
            </div>

            <!-- Columna Derecha: Sidebar (4 cols) -->
            <aside class="lg:col-span-4 space-y-12">

                <!-- Listas Públicas Destacadas -->
                @if($publicLists->isNotEmpty())
                    <section class="glass-premium rounded-[2.5rem] p-10 border-white/10 space-y-10">
                        <div class="space-y-1">
                            <h2 class="text-2xl font-black text-white tracking-tighter uppercase">Tendencias</h2>
                            <div class="h-1 w-12 bg-purple-600 rounded-full"></div>
                        </div>

                        <div class="space-y-6">
                            @foreach($publicLists as $list)
                                <a href="{{ route('media-lists.show', $list) }}" class="block group">
                                    <div class="flex gap-4 items-center">
                                        <div class="relative w-16 h-16 shrink-0">
                                            @if($list->items->first())
                                                <img src="{{ $list->items->first()->media->cover_url }}"
                                                    class="w-full h-full object-cover rounded-2xl border border-white/10" alt="">
                                            @else
                                                <div class="w-full h-full bg-white/5 rounded-2xl flex items-center justify-center">
                                                    <i class="fas fa-folder text-gray-700"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="text-white font-bold group-hover:text-blue-400 transition-colors truncate uppercase tracking-tight">
                                                {{ $list->name }}</h4>
                                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest truncate">POR
                                                {{ $list->user->username ?: 'USUARIO' }}</p>
                                        </div>
                                        <div class="text-[10px] font-black text-gray-700 uppercase">{{ $list->items->count() }}
                                            ITEMS</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </div>

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
                    button.classList.remove('text-gray-400', 'bg-gray-800');
                    button.querySelector('i').classList.replace('far', 'fas');
                } else {
                    button.classList.remove('text-red-500', 'bg-red-500/10');
                    button.classList.add('text-gray-400', 'bg-gray-800');
                    button.querySelector('i').classList.replace('fas', 'far');
                }

                button.querySelector('.like-count').textContent = data.count;
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        }

        function togglePostComments(postId) {
            const commentsDiv = document.getElementById('post-comments-' + postId);
            if (commentsDiv.classList.contains('hidden')) {
                commentsDiv.classList.remove('hidden');
            } else {
                commentsDiv.classList.add('hidden');
            }
        }
    </script>

    <script>
        const mediaTitleInput = document.getElementById('media_title');
        const mediaIdInput = document.getElementById('media_id');
        const suggestionsBox = document.getElementById('media-suggestions');

        if (mediaTitleInput) {
            let activeRequest = null;

            mediaTitleInput.addEventListener('input', async () => {
                const query = mediaTitleInput.value.trim();
                mediaIdInput.value = '';

                if (!query) {
                    suggestionsBox.classList.add('hidden');
                    suggestionsBox.innerHTML = '';
                    return;
                }

                if (activeRequest) {
                    activeRequest.abort();
                }

                activeRequest = new AbortController();

                try {
                    const response = await fetch(`{{ route('media.suggestions') }}?query=${encodeURIComponent(query)}`, {
                        signal: activeRequest.signal,
                    });

                    if (!response.ok) {
                        throw new Error('Error al buscar sugerencias');
                    }

                    const results = await response.json();
                    suggestionsBox.innerHTML = '';

                    if (results.length === 0) {
                        suggestionsBox.classList.add('hidden');
                        return;
                    }

                    suggestionsBox.classList.remove('hidden');

                    results.forEach(item => {
                        const suggestion = document.createElement('button');
                        suggestion.type = 'button';
                        suggestion.className = 'w-full text-left px-4 py-3 text-sm text-white hover:bg-gray-800 transition';
                        suggestion.innerHTML = `<span class="font-semibold">${item.title}</span>`;
                        suggestion.addEventListener('click', () => {
                            mediaTitleInput.value = item.title;
                            mediaIdInput.value = item.id;
                            suggestionsBox.classList.add('hidden');
                        });
                        suggestionsBox.appendChild(suggestion);
                    });
                } catch (error) {
                    console.error(error);
                    suggestionsBox.classList.add('hidden');
                }
            });

            document.addEventListener('click', event => {
                if (!mediaTitleInput.contains(event.target) && !suggestionsBox.contains(event.target)) {
                    suggestionsBox.classList.add('hidden');
                }
            });

            const toggleFormButton = document.getElementById('toggle-forum-form');
            const closeFormButton = document.getElementById('close-forum-form');
            const forumFormPanel = document.getElementById('forum-form-panel');

            if (toggleFormButton && forumFormPanel) {
                toggleFormButton.addEventListener('click', () => {
                    forumFormPanel.classList.toggle('hidden');
                    forumFormPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            if (closeFormButton && forumFormPanel) {
                closeFormButton.addEventListener('click', () => {
                    forumFormPanel.classList.add('hidden');
                });
            }
        }
    </script>
@endsection