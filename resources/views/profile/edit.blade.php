@extends('layouts.app')
@section('title', 'Mi Perfil')

@section('content')
    <div class="bg-gray-950 min-h-screen text-gray-100 selection:bg-blue-500/30 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Columna Derecha: Formularios (8 cols) -->
                <div class="lg:col-span-8 space-y-12">
                    <!-- Sección: Información Pública -->
                    <section id="info" class="glass-premium rounded-[2.5rem] p-10">
                        @include('profile.partials.update-profile-information-form')
                    </section>

                    <!-- Sección: Mi Colección -->
                    <section id="collection" class="glass-premium rounded-[2.5rem] p-10">
                        <div class="space-y-10">
                            <div class="space-y-1">
                                <h2 class="text-2xl font-black text-white tracking-tighter uppercase">Mi Colección</h2>
                                <p class="text-gray-500 text-sm font-medium">Gestiona los elementos que has guardado.</p>
                            </div>

                            @php
                                $userLists = \App\Models\UserList::where('user_id', Auth::id())->with('media')->get();
                            @endphp

                            @if($userLists->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                                    @foreach($userLists as $item)
                                        <div
                                            class="group relative aspect-[3/4] rounded-2xl overflow-hidden glass-premium hover:neon-border transition-all duration-500">
                                            <img src="{{ $item->media->cover_url }}" alt="{{ $item->media->title }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/20 to-transparent opacity-80">
                                            </div>
                                            <div class="absolute bottom-0 left-0 w-full p-4">
                                                <h4 class="font-bold text-[10px] text-white line-clamp-1 mb-2">
                                                    {{ $item->media->title }}</h4>
                                                <span
                                                    class="text-[8px] px-2 py-0.5 rounded-md bg-white/10 backdrop-blur-md text-white font-black uppercase tracking-tighter">
                                                    {{ $item->status }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-10 rounded-3xl border-2 border-dashed border-white/5 text-center">
                                    <p class="text-gray-500 text-sm">Tu colección está vacía.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <!-- Sección: Listas Personalizadas -->
                    <section id="lists" class="glass-premium rounded-[2.5rem] p-10">
                        <div class="space-y-10">
                            <div class="space-y-1">
                                <h2 class="text-2xl font-black text-white tracking-tighter uppercase">Listas Personalizadas
                                </h2>
                                <p class="text-gray-500 text-sm font-medium">Gestiona tus colecciones y su visibilidad.</p>
                            </div>

                            @php $mediaLists = \App\Models\MediaList::where('user_id', Auth::id())->withCount('items')->get(); @endphp
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($mediaLists as $list)
                                    <div
                                        class="p-6 rounded-3xl bg-white/5 flex items-center justify-between group hover:bg-white/10 transition-all">
                                        <div>
                                            <h4 class="text-white font-bold">{{ $list->name }}</h4>
                                            <p class="text-[10px] font-black text-gray-600 uppercase tracking-tighter">
                                                {{ $list->items_count }} ELEMENTOS</p>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center gap-2 text-red-500/60 font-bold text-xs">
                                                <i class="fas fa-heart"></i>
                                                <span>{{ $list->likes->count() }}</span>
                                            </div>
                                            <span
                                                class="text-[8px] font-black uppercase px-2 py-1 rounded-lg {{ $list->is_public ? 'bg-green-500/10 text-green-400' : 'bg-gray-600/10 text-gray-400' }}">
                                                {{ $list->is_public ? 'Pública' : 'Privada' }}
                                            </span>
                                            <form action="{{ route('media-lists.destroy', $list) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar lista?')">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-600 hover:text-red-500 transition-colors">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <!-- Sección: Seguridad -->
                    <section id="security" class="glass-premium rounded-[2.5rem] p-10 border-white/10">
                        @include('profile.partials.update-password-form')
                    </section>

                    <!-- Sección: Zona de Peligro -->
                    <section id="danger" class="glass-premium rounded-[2.5rem] p-10 border-red-900/20 bg-red-950/10">
                        @include('profile.partials.delete-user-form')
                    </section>
                </div>

            </div>
        </div>
    </div>
@endsection