@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pb-20">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-bold text-white tracking-tight">Foro de la comunidad</h1>
            <p class="mt-2 text-gray-400 max-w-2xl">Lee y comparte lo que estás viendo, tu lista de favoritos y tus recomendaciones con el resto de la comunidad.</p>
        </div>
        <p class="text-xs text-gray-600 hidden md:block">Publicaciones recientes de usuarios registrados.</p>
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
                    <button id="toggle-forum-form" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded-xl transition shadow-lg shadow-blue-900/20">
                        NUEVA PUBLICACIÓN
                    </button>
                </div>

                <div id="forum-form-panel" class="bg-gray-900/50 rounded-3xl p-8 border border-white/5 mb-10 {{ old('title') || old('body') || old('media_title') || old('media_id') ? '' : 'hidden' }}">
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
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Categoría</label>
                                <select name="category" class="w-full bg-gray-950 border border-white/10 rounded-xl p-3 text-white focus:ring-2 focus:ring-blue-600">
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', 'general') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Título</label>
                                <input name="title" type="text" value="{{ old('title') }}" class="w-full bg-gray-950 border border-white/10 rounded-xl p-3 text-white focus:ring-2 focus:ring-blue-600" placeholder="Título de tu post">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Contenido</label>
                            <textarea name="body" rows="4" class="w-full bg-gray-950 border border-white/10 rounded-xl p-3 text-white focus:ring-2 focus:ring-blue-600" placeholder="Escribe aquí..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="relative space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Vincular Contenido</label>
                                <input id="media_title" name="media_title" type="text" value="{{ old('media_title') }}" autocomplete="off" class="w-full bg-gray-950 border border-white/10 rounded-xl p-3 text-white focus:ring-2 focus:ring-blue-600" placeholder="Busca algo...">
                                <input id="media_id" name="media_id" type="hidden" value="{{ old('media_id') }}">
                                <div id="media-suggestions" class="absolute z-50 mt-1 w-full rounded-xl border border-white/10 bg-gray-950 shadow-2xl hidden max-h-64 overflow-y-auto"></div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Adjuntar Imagen</label>
                                <input name="attachment" type="file" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-white/5 file:text-white" />
                            </div>
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-8 py-3 rounded-xl transition">
                            PUBLICAR
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-blue-900/10 border border-blue-500/10 rounded-2xl p-8 text-center mb-10">
                    <p class="text-gray-300 font-medium text-sm">Inicia sesión para compartir publicaciones con la comunidad.</p>
                </div>
            @endauth

            <!-- Filtros -->
            <div class="flex flex-wrap gap-3 mb-10">
                @foreach($categories as $key => $label)
                    <a href="{{ route('forum.index', ['category' => $key]) }}" 
                       class="px-5 py-2 rounded-full text-sm font-medium transition-all {{ $selectedCategory === $key ? 'bg-blue-600 text-white' : 'bg-gray-900 text-gray-400 hover:text-white border border-white/5' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="space-y-6">
                @forelse($posts as $post)
                    <article class="bg-gray-900/40 border border-white/5 rounded-3xl p-8 hover:bg-gray-900/60 transition-all group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex gap-4 items-center">
                                <img src="{{ $post->user->avatar_url }}" class="w-10 h-10 rounded-xl object-cover border border-white/5" alt="">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Publicado por</p>
                                    <a href="{{ route('users.show', $post->user) }}" class="text-white font-bold hover:text-blue-400 transition-colors">{{ $post->user->username ?: $post->user->name }}</a>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-gray-600 uppercase">{{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="space-y-4 mb-6">
                            <span class="inline-block bg-white/5 text-gray-400 text-[9px] font-black uppercase px-2 py-1 rounded-md tracking-wider">
                                {{ $post->category ? str_replace('_', ' ', $post->category) : 'GENERAL' }}
                            </span>
                            <h3 class="text-xl font-bold text-white group-hover:text-blue-400 transition-colors">{{ $post->title }}</h3>
                            <p class="text-gray-400 text-sm leading-relaxed">{{ $post->body }}</p>
                        </div>

                        @if($post->media)
                            <a href="{{ route('media.show', $post->media) }}" class="inline-flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all mb-6">
                                <div class="w-8 h-8 rounded-lg bg-blue-600/20 flex items-center justify-center text-blue-400">
                                    <i class="fas fa-link text-xs"></i>
                                </div>
                                <span class="text-xs font-bold text-white">{{ $post->media->title }}</span>
                            </a>
                        @endif

                        @if($post->attachment_path)
                            @php
                                $attachmentUrl = asset('storage/' . $post->attachment_path);
                            @endphp
                            <div class="pt-2 border-t border-white/5 mt-6">
                                <p class="text-[10px] font-bold text-gray-600 uppercase mb-3">Archivo adjunto</p>
                                <div class="rounded-xl overflow-hidden border border-white/5 bg-black/20">
                                    <img src="{{ $attachmentUrl }}" class="w-full object-cover max-h-48" alt="">
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-white/5">
                            <button onclick="toggleLike({{ $post->id }}, 'post', this)" 
                                    class="flex items-center gap-2 text-gray-500 hover:text-white transition-colors">
                                <i class="{{ auth()->user() && $post->isLikedBy(auth()->user()) ? 'fas text-red-500' : 'far' }} fa-heart text-sm"></i>
                                <span class="like-count font-bold text-sm">{{ $post->likes()->count() }}</span>
                            </button>
                            
                            @if(auth()->check() && (auth()->id() === $post->user_id || auth()->user()->role === 'admin'))
                                <form action="{{ route('forum.destroy', $post) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-600 hover:text-red-500 transition-colors text-sm">
                                        Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="p-12 text-center bg-gray-900/20 rounded-3xl border border-white/5">
                        <p class="text-gray-500 font-medium">No hay publicaciones todavía.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
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
                                            <img src="{{ $list->items->first()->media->cover_url }}" class="w-full h-full object-cover rounded-2xl border border-white/10" alt="">
                                        @else
                                            <div class="w-full h-full bg-white/5 rounded-2xl flex items-center justify-center">
                                                <i class="fas fa-folder text-gray-700"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-white font-bold group-hover:text-blue-400 transition-colors truncate uppercase tracking-tight">{{ $list->name }}</h4>
                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest truncate">POR {{ $list->user->username ?: 'USUARIO' }}</p>
                                    </div>
                                    <div class="text-[10px] font-black text-gray-700 uppercase">{{ $list->items->count() }} ITEMS</div>
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
