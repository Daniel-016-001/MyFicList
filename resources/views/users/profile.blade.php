@extends('layouts.app')

@section('content')
    <div class="bg-gray-950 min-h-screen text-gray-100 selection:bg-blue-500/30">
        <!-- Header Cinematográfico del Perfil -->
        <div class="relative w-full overflow-hidden">
            <div class="absolute inset-0 h-[500px] bg-gradient-to-b from-blue-600/20 via-purple-600/10 to-gray-950"></div>
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 blur-[120px] rounded-full -mr-20 -mt-20 animate-pulse">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20 relative z-10">
                <div class="flex flex-col lg:flex-row items-center lg:items-end gap-12">
                    <!-- Avatar Cinematic -->
                    <div class="relative group/avatar">
                        <div
                            class="absolute -inset-1.5 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-[2.5rem] blur opacity-25 group-hover/avatar:opacity-60 transition duration-1000">
                        </div>
                        <div
                            class="relative w-56 h-56 rounded-[2.5rem] overflow-hidden border-4 border-gray-950 bg-gray-900 shadow-2xl">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover/avatar:scale-110">
                            @if(auth()->id() === $user->id)
                                <a href="{{ route('profile.edit') }}"
                                    class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 group-hover/avatar:opacity-100 transition-all duration-300 flex items-center justify-center">
                                    <div class="bg-white text-black p-4 rounded-2xl shadow-xl">
                                        <i class="fas fa-camera text-xl"></i>
                                    </div>
                                </a>
                            @endif
                        </div>
                        <div
                            class="absolute -bottom-4 -right-4 glass-premium px-6 py-2 rounded-2xl border-white/20 shadow-2xl shimmer">
                            <span class="text-xs font-black text-white uppercase tracking-tighter">LVL
                                {{ floor($totalCompleted / 5) + 1 }}</span>
                        </div>
                    </div>

                    <!-- Info Info -->
                    <div class="flex-1 text-center lg:text-left space-y-8">
                        <div class="space-y-4">
                            <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4">
                                <h1 class="text-6xl font-black text-white tracking-tighter uppercase drop-shadow-2xl">
                                    {{ $user->username }}</h1>
                                <span class="badge-primary">Miembro</span>
                            </div>
                            <p class="text-xl text-gray-400 font-medium max-w-2xl leading-relaxed">
                                "{{ $user->bio ?? 'Este usuario prefiere el misterio...' }}"</p>
                        </div>

                        <!-- Stats Bar -->
                        <div class="flex flex-wrap justify-center lg:justify-start gap-12 pt-4">
                            <div class="space-y-1 group">
                                <p
                                    class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] group-hover:text-blue-400 transition-colors">
                                    Completados</p>
                                <p class="text-3xl font-black text-white tracking-tighter">{{ $totalCompleted }}</p>
                            </div>
                            <div class="space-y-1 group">
                                <p
                                    class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] group-hover:text-red-400 transition-colors">
                                    Reconocimiento</p>
                                <p class="text-3xl font-black text-white tracking-tighter flex items-center gap-2">
                                    <i class="fas fa-heart text-red-500 text-xl"></i> {{ $totalLikes }}
                                </p>
                            </div>
                            <div class="space-y-1 group cursor-pointer" onclick="window.toggleModal('followers-modal')">
                                <p
                                    class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] group-hover:text-purple-400 transition-colors">
                                    Seguidores</p>
                                <p class="text-3xl font-black text-white tracking-tighter">{{ $followers->count() }}
                                </p>
                            </div>
                            <div class="space-y-1 group cursor-pointer" onclick="window.toggleModal('following-modal')">
                                <p
                                    class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] group-hover:text-purple-400 transition-colors">
                                    Siguiendo</p>
                                <p class="text-3xl font-black text-white tracking-tighter">{{ $following->count() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex gap-4">
                        @if(auth()->id() === $user->id)
                            <a href="{{ route('profile.edit') }}"
                                class="bg-white text-black font-black px-10 py-5 rounded-2xl hover:bg-gray-200 transition-all hover:scale-105 active:scale-95 shadow-xl shadow-white/5 uppercase tracking-tighter">
                                Ajustes Perfil
                            </a>
                        @else
                            <form action="{{ route('users.follow', $user) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-500 text-white font-black px-12 py-5 rounded-2xl transition-all hover:scale-105 active:scale-95 shadow-xl shadow-blue-900/20 uppercase tracking-tighter">
                                    {{ auth()->user() && auth()->user()->following->contains($user->id) ? 'DEJAR DE SEGUIR' : 'SEGUIR' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                <!-- Columna Izquierda: Colecciones (8 cols) -->
                <div class="lg:col-span-8 space-y-16">
                    <div class="flex items-end justify-between border-b border-white/5 pb-8">
                        <div class="space-y-1">
                            <h2 class="text-3xl font-black text-white tracking-tighter uppercase">Colecciones Públicas</h2>
                            <p class="text-gray-500 text-sm font-medium">Creadas por {{ $user->username }}</p>
                        </div>
                        <div class="text-gray-600 text-xs font-black tracking-widest uppercase">{{ $mediaLists->count() }}
                            LISTAS</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @forelse($mediaLists as $list)
                            <a href="{{ route('media-lists.show', $list) }}"
                                class="group relative block aspect-video rounded-[2.5rem] overflow-hidden glass-premium hover:neon-border transition-all duration-500">
                                <!-- Grid de Fondo -->
                                <div
                                    class="absolute inset-0 grid grid-cols-3 grid-rows-1 gap-0.5 opacity-30 group-hover:opacity-50 transition-all duration-700">
                                    @foreach($list->items->take(3) as $item)
                                        <img src="{{ $item->media->cover_url }}" class="w-full h-full object-cover" alt="">
                                    @endforeach
                                    @if($list->items->isEmpty())
                                        <div class="col-span-3 bg-gray-950 flex items-center justify-center">
                                            <i class="fas fa-folder-open text-4xl text-gray-800"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/40 to-transparent">
                                </div>

                                <div
                                    class="absolute inset-0 p-10 flex flex-col justify-end space-y-3 translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                    <div class="flex items-center justify-between">
                                        <h3
                                            class="text-3xl font-black text-white tracking-tighter group-hover:text-blue-400 transition-colors uppercase">
                                            {{ $list->name }}</h3>
                                        <div
                                            class="glass-premium px-4 py-1.5 rounded-xl border-white/10 text-[10px] font-black text-white">
                                            {{ $list->items->count() }} ITEMS
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center gap-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        <span class="flex items-center gap-2"><i class="fas fa-heart text-red-500"></i>
                                            {{ $list->likes->count() }}</span>
                                        <span>•</span>
                                        <span>Actualizado {{ $list->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div
                                class="col-span-full py-32 rounded-[3rem] border-2 border-dashed border-white/5 flex flex-col items-center justify-center space-y-6 text-center">
                                <div
                                    class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center text-gray-700 text-4xl">
                                    <i class="fas fa-ghost"></i>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-white font-black text-2xl uppercase tracking-tighter">Sin rastro de listas
                                    </p>
                                    <p class="text-gray-500 font-medium">Este usuario mantiene su perfil privado.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Columna Derecha: Activity Feed (4 cols) -->
                <div class="lg:col-span-4 space-y-12">
                    <section class="glass-premium rounded-[3rem] p-10 space-y-10 border-white/10 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 blur-3xl rounded-full"></div>

                        <div class="space-y-1">
                            <h2 class="text-2xl font-black text-white tracking-tighter uppercase">Actividad Reciente</h2>
                            <div class="h-1 w-12 bg-blue-600 rounded-full"></div>
                        </div>

                        <div class="space-y-12 relative">
                            <!-- Linea de tiempo -->
                            <div class="absolute left-3.5 top-0 bottom-0 w-px bg-white/5"></div>

                            @php $hasActivity = false; @endphp

                            <!-- Actividad: Comentarios -->
                            @foreach($recentComments as $comment)
                                @php $hasActivity = true; @endphp
                                <div class="relative pl-12 space-y-4">
                                    <div
                                        class="absolute left-0 top-1 w-7 h-7 rounded-full glass-premium border-white/20 flex items-center justify-center z-10 shadow-xl shimmer">
                                        <i class="fas fa-comment text-[10px] text-blue-400"></i>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="space-y-0.5">
                                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-[0.2em]">Crítica
                                            </p>
                                            <p class="text-sm text-gray-400 font-medium leading-relaxed">
                                                Opinó sobre
                                                @if($comment->media)
                                                    <a href="{{ route('media.show', $comment->media) }}"
                                                        class="text-white font-black hover:text-blue-400 transition-colors uppercase">{{ $comment->media->title }}</a>
                                                @else
                                                    <span class="text-gray-500 italic uppercase">un título eliminado</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div
                                            class="bg-white/5 p-5 rounded-2xl border border-white/5 text-gray-400 text-xs leading-relaxed group hover:bg-white/10 transition-colors">
                                            "{{ Str::limit($comment->content, 120) }}"
                                        </div>
                                        <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest">
                                            {{ $comment->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Actividad: Listas -->
                            @foreach($recentListItems as $item)
                                @php $hasActivity = true; @endphp
                                <div class="relative pl-12 space-y-4">
                                    <div
                                        class="absolute left-0 top-1 w-7 h-7 rounded-full glass-premium border-white/20 flex items-center justify-center z-10 shadow-xl shimmer">
                                        <i class="fas fa-plus text-[10px] text-green-400"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="space-y-0.5">
                                            <p class="text-[9px] font-black text-green-600 uppercase tracking-[0.2em]">Colección
                                            </p>
                                            <p class="text-sm text-gray-400 font-medium">
                                                Añadió <span
                                                    class="text-white font-black uppercase">{{ $item->media->title }}</span>
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="badge-success">{{ $item->status }}</span>
                                            <span
                                                class="text-[9px] font-black text-gray-600 uppercase">{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Actividad: Foro -->
                            @foreach($recentPosts as $post)
                                @php $hasActivity = true; @endphp
                                <div class="relative pl-12 space-y-4">
                                    <div
                                        class="absolute left-0 top-1 w-7 h-7 rounded-full glass-premium border-white/20 flex items-center justify-center z-10 shadow-xl shimmer">
                                        <i class="fas fa-bullhorn text-[10px] text-purple-400"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="space-y-0.5">
                                            <p class="text-[9px] font-black text-purple-600 uppercase tracking-[0.2em]">Foro</p>
                                            <p class="text-sm text-gray-400 font-medium leading-relaxed">
                                                Publicó <span
                                                    class="text-white font-black uppercase">"{{ $post->title }}"</span>
                                            </p>
                                        </div>
                                        <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest">
                                            {{ $post->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach

                            @if(!$hasActivity)
                                <div class="text-center py-20 space-y-4">
                                    <div class="text-gray-800 text-6xl"><i class="fas fa-hourglass-start"></i></div>
                                    <p class="text-gray-600 text-xs font-black uppercase tracking-widest">Sin rastro de
                                        actividad</p>
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <!-- Modales de Seguidores y Siguiendo -->
        <!-- Modal Seguidores -->
        <div id="followers-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/70 backdrop-blur-md" onclick="window.toggleModal('followers-modal')"></div>
            <!-- Content -->
            <div class="relative w-[92%] sm:w-full max-w-md rounded-[2.5rem] bg-gray-900 border border-gray-800 p-8 shadow-2xl shadow-black/80 overflow-hidden max-h-[80vh] flex flex-col z-10">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 blur-3xl rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between border-b border-white/5 pb-4 mb-6 relative z-10">
                    <h3 class="text-2xl font-black text-white tracking-tighter uppercase">Seguidores</h3>
                    <button type="button" onclick="window.toggleModal('followers-modal')" class="relative z-20 text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <!-- List -->
                <div class="overflow-y-auto pr-2 space-y-4 flex-1">
                    @forelse($followers as $f)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all duration-300">
                            <a href="{{ route('users.show', $f) }}" class="flex items-center gap-3 group">
                                <img src="{{ $f->avatar_url }}" alt="{{ $f->username }}" class="w-12 h-12 rounded-xl object-cover border border-white/10 group-hover:scale-105 transition-transform">
                                <span class="font-bold text-white group-hover:text-blue-400 transition-colors uppercase tracking-tight text-sm">{{ $f->username }}</span>
                            </a>
                            @if(auth()->check() && auth()->id() !== $f->id)
                                <form action="{{ route('users.follow', $f) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-black px-4 py-2 rounded-xl border border-white/10 transition-all hover:scale-105 active:scale-95 uppercase tracking-wider {{ auth()->user()->following->contains($f->id) ? 'bg-white/10 text-white hover:bg-white/20' : 'bg-blue-600 text-white hover:bg-blue-500' }}">
                                        {{ auth()->user()->following->contains($f->id) ? 'Siguiendo' : 'Seguir' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-users text-3xl mb-2 block"></i>
                            <p class="text-xs uppercase font-black tracking-widest">Sin seguidores todavía</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Modal Siguiendo -->
        <div id="following-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/70 backdrop-blur-md" onclick="window.toggleModal('following-modal')"></div>
            <!-- Content -->
            <div class="relative w-[92%] sm:w-full max-w-md rounded-[2.5rem] bg-gray-900 border border-gray-800 p-8 shadow-2xl shadow-black/80 overflow-hidden max-h-[80vh] flex flex-col z-10">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-600/10 blur-3xl rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between border-b border-white/5 pb-4 mb-6 relative z-10">
                    <h3 class="text-2xl font-black text-white tracking-tighter uppercase">Siguiendo</h3>
                    <button type="button" onclick="window.toggleModal('following-modal')" class="relative z-20 text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <!-- List -->
                <div class="overflow-y-auto pr-2 space-y-4 flex-1">
                    @forelse($following as $f)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all duration-300">
                            <a href="{{ route('users.show', $f) }}" class="flex items-center gap-3 group">
                                <img src="{{ $f->avatar_url }}" alt="{{ $f->username }}" class="w-12 h-12 rounded-xl object-cover border border-white/10 group-hover:scale-105 transition-transform">
                                <span class="font-bold text-white group-hover:text-blue-400 transition-colors uppercase tracking-tight text-sm">{{ $f->username }}</span>
                            </a>
                            @if(auth()->check() && auth()->id() !== $f->id)
                                <form action="{{ route('users.follow', $f) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-black px-4 py-2 rounded-xl border border-white/10 transition-all hover:scale-105 active:scale-95 uppercase tracking-wider {{ auth()->user()->following->contains($f->id) ? 'bg-white/10 text-white hover:bg-white/20' : 'bg-blue-600 text-white hover:bg-blue-500' }}">
                                        {{ auth()->user()->following->contains($f->id) ? 'Siguiendo' : 'Seguir' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-users text-3xl mb-2 block"></i>
                            <p class="text-xs uppercase font-black tracking-widest">No sigue a nadie todavía</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        window.toggleModal = function(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }

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
                    button.classList.add('text-red-500');
                    button.querySelector('i').classList.replace('far', 'fas');
                } else {
                    button.classList.remove('text-red-500');
                    button.querySelector('i').classList.replace('fas', 'far');
                }

                button.querySelector('.like-count').textContent = data.count;
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        }
    </script>
@endsection