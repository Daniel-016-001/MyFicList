@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-16">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Foro de la comunidad</h1>
            <p class="mt-2 text-gray-400 max-w-2xl">Lee y comparte lo que estás viendo, tu lista de favoritos y tus recomendaciones con el resto de la comunidad.</p>
        </div>
        <div class="text-sm text-gray-500">
            Publicaciones recientes de usuarios registrados.
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-3xl border border-green-500 bg-green-900/70 p-4 text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @auth
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-white">Publicaciones</h2>
                <p class="text-gray-400">Desliza sin distracciones. Abre el formulario solo cuando quieras publicar.</p>
            </div>
            <button id="toggle-forum-form" class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-500 transition">
                <i class="fas fa-plus"></i> Nueva publicación
            </button>
        </div>

        <div id="forum-form-panel" class="mb-8 rounded-3xl border border-gray-800 bg-gray-900 p-6 shadow-sm {{ old('title') || old('body') || old('media_title') || old('media_id') ? '' : 'hidden' }}">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-white">Crear nueva publicación</h2>
                    <p class="text-gray-400 mt-1">Comparte tu lista, recomendaciones o lo que quieras con la comunidad.</p>
                </div>
                <button id="close-forum-form" type="button" class="inline-flex items-center gap-2 rounded-full bg-gray-800 px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('forum.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-200">Categoría</label>
                    <select id="category" name="category" class="mt-2 w-full rounded-2xl border border-gray-700 bg-gray-950 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', 'general') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-200">Título</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" class="mt-2 w-full rounded-2xl border border-gray-700 bg-gray-950 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="¿Sobre qué quieres hablar?">
                    @error('title')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-gray-200">Contenido</label>
                    <textarea id="body" name="body" rows="5" class="mt-2 w-full rounded-2xl border border-gray-700 bg-gray-950 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Escribe lo que quieras compartir...">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="relative">
                    <label for="media_title" class="block text-sm font-medium text-gray-200">Vincular a contenido</label>
                    <input id="media_title" name="media_title" type="text" value="{{ old('media_title') }}" autocomplete="off" class="mt-2 w-full rounded-2xl border border-gray-700 bg-gray-950 px-4 py-3 text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Busca por nombre de anime, película o juego">
                    <input id="media_id" name="media_id" type="hidden" value="{{ old('media_id') }}">
                    <div id="media-suggestions" class="absolute z-50 mt-1 w-full rounded-2xl border border-gray-700 bg-gray-950 shadow-xl hidden max-h-64 overflow-y-auto"></div>
                    <p class="mt-2 text-sm text-gray-500">Selecciona un resultado sugerido para vincularlo a tu publicación.</p>
                    @error('media_id')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-200">Archivo adjunto</label>
                    <input id="attachment" name="attachment" type="file" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip" class="mt-2 w-full text-sm text-gray-200 file:mr-4 file:rounded-full file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-500" />
                    @error('attachment')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-500 transition">
                    Publicar en el foro
                </button>
            </form>
        </div>
    @else
        <div class="rounded-3xl border border-gray-800 bg-gray-900 p-6 text-center text-gray-300 mb-8">
            Inicia sesión para compartir publicaciones con la comunidad.
        </div>
    @endauth

    @if($publicLists->isNotEmpty())
        <div class="mb-10">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-star text-yellow-500"></i>
                <h2 class="text-xl font-semibold text-white">Listas Públicas Recientes</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                @foreach($publicLists as $list)
                    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden hover:border-blue-500 transition shadow-sm flex flex-col group cursor-pointer" onclick="window.location='{{ route('media-lists.show', $list) }}'">
                        <div class="p-4 border-b border-gray-800 flex items-center justify-between bg-gray-800/20">
                            <div class="flex flex-col truncate pr-2">
                                <span class="font-bold text-sm text-white group-hover:text-blue-400 truncate transition-colors">{{ $list->name }}</span>
                                <span class="text-[10px] text-gray-500 uppercase tracking-wider truncate">Por {{ $list->user->username ?? 'Usuario' }}</span>
                            </div>
                            <span class="text-xs font-semibold bg-gray-800 text-gray-300 px-2 py-1 rounded-lg flex-shrink-0">{{ $list->items->count() }} items</span>
                        </div>
                        <div class="p-4 flex gap-2 overflow-hidden flex-grow bg-gray-950">
                            @foreach($list->items->take(4) as $item)
                                <img src="{{ $item->media->cover_url }}" alt="" class="w-12 h-16 object-cover rounded shadow-sm opacity-90 group-hover:opacity-100 transition">
                            @endforeach
                            @if($list->items->isEmpty())
                                <div class="text-xs text-gray-600 italic w-full text-center py-4">Lista vacía</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mb-6 flex flex-wrap gap-3">
        @foreach($categories as $key => $label)
            <a href="{{ route('forum.index', ['category' => $key]) }}" class="rounded-full border px-4 py-2 text-sm font-medium transition {{ $selectedCategory === $key ? 'border-blue-500 bg-blue-600 text-white' : 'border-gray-700 bg-gray-900 text-gray-300 hover:border-blue-500 hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-6">
        @forelse($posts as $post)
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6 shadow-sm hover:border-blue-500 transition">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-sm text-gray-400">Publicado por</div>
                        @if($post->user && !empty($post->user->username))
                            <a href="{{ route('users.show', $post->user->username) }}" class="text-lg font-semibold text-white hover:text-blue-400">
                                {{ $post->user->username }}
                            </a>
                        @elseif($post->user)
                            <span class="text-lg font-semibold text-white">{{ $post->user->name ?: 'Usuario' }}</span>
                        @else
                            <span class="text-lg font-semibold text-gray-400">Anónimo</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-400">{{ $post->created_at->diffForHumans() }}</div>
                </div>

                <div class="mt-5">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="rounded-full bg-gray-800 px-3 py-1 text-xs uppercase tracking-wide text-gray-300">{{ $post->category ? ucfirst(str_replace('_', ' ', $post->category)) : 'General' }}</span>
                    </div>
                    <h3 class="text-xl font-semibold text-white">{{ $post->title }}</h3>
                    <p class="mt-3 text-gray-300 whitespace-pre-line">{{ $post->body }}</p>
                </div>

                @if($post->media)
                    <div class="mt-5 rounded-2xl bg-gray-950 border border-gray-800 p-4">
                        <div class="text-sm text-gray-400">Relacionado con</div>
                        <a href="{{ route('media.show', $post->media->id) }}" class="text-blue-400 hover:text-blue-300">{{ $post->media->title ?? 'Media desconocida' }}</a>
                    </div>
                @endif

                @if($post->attachment_path)
                    @php
                        $attachmentUrl = asset('storage/' . $post->attachment_path);
                        $extension = strtolower(pathinfo($post->attachment_path, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                    @endphp
                    <div class="mt-5 rounded-2xl bg-gray-950 border border-gray-800 p-4">
                        <div class="text-sm text-gray-400">Archivo adjunto</div>
                        @if($isImage)
                            <a href="{{ $attachmentUrl }}" target="_blank" class="block mt-3 rounded-xl overflow-hidden border border-gray-800">
                                <img src="{{ $attachmentUrl }}" alt="Adjunto" class="w-full object-cover" />
                            </a>
                        @else
                            <a href="{{ $attachmentUrl }}" target="_blank" class="mt-3 inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-500 transition">
                                <i class="fas fa-file-arrow-down"></i>Descargar archivo adjunto
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 text-center">
                <p class="text-gray-400">No hay publicaciones todavía. Vuelve pronto para ver nuevas recomendaciones y actualizaciones.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $posts->links() }}
    </div>
</div>

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