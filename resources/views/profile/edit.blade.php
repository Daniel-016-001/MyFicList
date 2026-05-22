@extends('layouts.app')

@section('title', 'Ajustes de Perfil')

@section('content')
    <div class="bg-gray-950 min-h-screen text-gray-100 selection:bg-blue-500/30 pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 space-y-12">
            <!-- Tu Perfil Público -->
            <section id="public-profile" class="glass-premium rounded-[2.5rem] p-10">
                <div class="space-y-1 mb-8">
                    <h2 class="text-2xl font-black text-white tracking-tighter uppercase">Tu Perfil Público</h2>
                    <p class="text-gray-500 text-sm font-medium">Así es como otros usuarios te ven.</p>
                </div>
                
                <!-- Header Cinematográfico del Perfil -->
                <div class="relative rounded-2xl overflow-hidden bg-gradient-to-b from-blue-600/20 via-purple-600/10 to-gray-900 p-8">
                    <div class="flex flex-col lg:flex-row items-center lg:items-end gap-8">
                        <!-- Avatar -->
                        <div class="relative group/avatar">
                            <div class="relative w-40 h-40 rounded-2xl overflow-hidden border-4 border-gray-950 bg-gray-900 shadow-2xl">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->username }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="absolute -bottom-2 -right-2 glass-premium px-4 py-1 rounded-xl border-white/20 shadow-lg">
                                <span class="text-xs font-black text-white uppercase tracking-tighter">LVL {{ floor($totalCompleted / 5) + 1 ?? 1 }}</span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 text-center lg:text-left space-y-4">
                            <div class="space-y-2">
                                <h1 class="text-4xl font-black text-white tracking-tighter uppercase">{{ Auth::user()->username }}</h1>
                                <p class="text-gray-400 font-medium max-w-lg leading-relaxed">"{{ Auth::user()->bio ?? 'Este usuario prefiere el misterio...' }}"</p>
                            </div>

                            <!-- Stats -->
                            <div class="flex flex-wrap gap-8 pt-4">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">Completados</p>
                                    <p class="text-2xl font-black text-white tracking-tighter">{{ $totalCompleted ?? 0 }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">Reconocimiento</p>
                                    <p class="text-2xl font-black text-white tracking-tighter"><i class="fas fa-heart text-red-500 mr-2"></i>{{ $totalLikes ?? 0 }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">Seguidores</p>
                                    <p class="text-2xl font-black text-white tracking-tighter">{{ Auth::user()->followers()->count() ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Sección: Editar Información Pública -->
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

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" maxWidth="lg" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-gray-900 border border-gray-800 rounded-[2.5rem]">
            @csrf
            @method('delete')

            <h2 class="text-xl font-bold text-white mb-2">
                ¿Estás seguro de que quieres eliminar tu cuenta?
            </h2>

            <p class="text-sm text-gray-400 mb-6">
                Una vez que tu cuenta sea eliminada, todos sus recursos y datos se borrarán permanentemente. Por favor, introduce tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.
            </p>

            <div class="mt-6" x-data="{ show: false }">
                <label for="password" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                    Contraseña
                </label>
                <div class="relative w-full">
                    <input id="password" name="password" :type="show ? 'text' : 'password'"
                        style="color: white !important; background-color: #030712 !important;"
                        class="mt-1 block w-full px-4 py-3 focus:ring-2 focus:ring-red-600 rounded-xl shadow-sm border border-gray-800 pr-12"
                        placeholder="Tu contraseña" />
                    <button type="button" @click="show = !show"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-400" />
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white font-bold rounded-xl transition">
                    Cancelar
                </button>

                <button type="submit"
                    class="px-6 py-3 bg-red-600 hover:bg-red-500 text-white font-black rounded-xl transition shadow-lg shadow-red-900/20">
                    Eliminar cuenta
                </button>
            </div>
        </form>
    </x-modal>
@endsection