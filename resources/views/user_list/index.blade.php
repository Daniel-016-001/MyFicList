@extends('layouts.app')
@section('title', 'Mi Lista')

@section('content')
    <div class="bg-gray-950 min-h-screen text-gray-100">
        <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Mis listas</h1>
                    <p class="text-gray-400">Administra tus colecciones y controla qué listas puedes compartir.</p>
                </div>
                <a href="{{ route('media.explore') }}"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition">
                    <i class="fas fa-compass"></i> Explorar catálogo
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl bg-green-900/70 p-4 text-green-100">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-xl bg-red-900/70 p-4 text-red-100">
                    {{ session('error') }}
                </div>
            @endif

            @if($mediaLists->isEmpty())
                <div class="rounded-3xl bg-gray-900 p-16 text-center">
                    <p class="text-gray-400 text-xl mb-6">Aún no tienes listas creadas.</p>
                    <p class="text-gray-500 text-sm mb-8">Explora el catálogo y agrega contenido para comenzar a crear tus
                        colecciones.</p>
                    <a href="{{ route('media.explore') }}"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white py-3 px-8 rounded-xl transition font-bold shadow-lg">
                        <i class="fas fa-search"></i> Explorar catálogo
                    </a>
                </div>
            @else
                <div class="space-y-8">
                    @foreach($mediaLists as $list)
                        <section class="rounded-3xl bg-gray-900/60 p-6">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h2 class="text-2xl font-bold text-white">{{ $list->name }}</h2>

                                    <form action="{{ route('media-lists.update', $list) }}" method="POST" class="inline-block mt-1">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" value="{{ $list->name }}">
                                        <input type="hidden" name="is_public" value="{{ $list->is_public ? '0' : '1' }}">

                                        <div class="flex items-center gap-3">
                                            <button type="submit"
                                                style="height: 28px; width: 48px; padding: 2px;"
                                                class="relative inline-flex flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $list->is_public ? 'bg-blue-600' : 'bg-gray-700' }}"
                                                role="switch">
                                                <span
                                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out flex items-center justify-center {{ $list->is_public ? 'translate-x-5' : 'translate-x-0' }}">
                                                    @if($list->is_public)
                                                        <i class="fas fa-globe-americas text-blue-600" style="font-size: 10px;"></i>
                                                    @else
                                                        <i class="fas fa-lock text-gray-500" style="font-size: 10px;"></i>
                                                    @endif
                                                </span>
                                            </button>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-xs font-bold {{ $list->is_public ? 'text-blue-400' : 'text-gray-400' }} uppercase tracking-wider">
                                                    {{ $list->is_public ? 'Pública' : 'Privada' }}
                                                </span>
                                                <span class="text-[10px] text-gray-500 leading-tight">
                                                    {{ $list->is_public ? 'Visible en el foro para todos' : 'Solo tú puedes ver esta lista' }}
                                                </span>
                                            </div>
                                        </div>
                                    </form>

                                    <span class="text-sm text-gray-500">{{ $list->items->count() }}
                                        elemento{{ $list->items->count() !== 1 ? 's' : '' }}</span>
                                </div>
                                <a href="{{ route('media-lists.show', $list) }}"
                                    class="inline-flex items-center gap-2 bg-blue-600/20 hover:bg-blue-600/40 text-blue-400 hover:text-white px-4 py-2 rounded-xl transition font-bold text-sm">
                                    <i class="fas fa-eye"></i> Ver lista completa
                                </a>
                            </div>

                            @if($list->items->isEmpty())
                                <div class="rounded-2xl bg-gray-900 p-8 text-center text-gray-500">
                                    Esta lista está vacía. Agrega contenido desde <a href="{{ route('media.explore') }}"
                                        class="text-blue-400 hover:underline">Explorar</a>.
                                </div>
                            @else
                                <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-4">
                                    @foreach($list->items as $entry)
                                        @php $media = $entry->media; @endphp
                                        <div class="break-inside-avoid mb-4">
                                            <div
                                                class="bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.02] transform-gpu flex flex-col">
                                                @if($media)
                                                    <a href="{{ route('media.show', $media->id) }}" class="block group">
                                                @else
                                                    <div class="block group opacity-50 cursor-not-allowed">
                                                @endif
                                                    <div class="relative">
                                                        <img src="{{ $media ? $media->cover_url : '' }}" alt="{{ $media ? $media->title : 'N/A' }}"
                                                            class="w-full h-auto object-cover brightness-90 group-hover:brightness-100 transition-all rounded-t-2xl">
                                                        @if($entry->score)
                                                            <div style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.75); border-radius: 0.5rem; padding: 0.25rem 0.5rem; z-index: 10; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;"
                                                                class="backdrop-blur-sm shadow-lg text-yellow-400 text-xs font-black">
                                                                <i class="fas fa-star text-[9px]"></i> {{ $entry->score }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @if($media) </a> @else </div> @endif
                                                
                                                <div class="p-4 flex-grow flex flex-col">
                                                    <h3 class="font-bold text-sm text-white leading-tight mb-2">{{ $media->title }}</h3>
                                                    <div class="flex flex-col gap-1 mb-3 text-[10px] font-bold uppercase tracking-wider">
                                                        <span
                                                            class="text-blue-400">{{ ucfirst(str_replace('_', ' ', $entry->status)) }}</span>
                                                        @if($media->media_type !== 'game' && $entry->progress)
                                                            <!-- Progreso eliminado por solicitud del usuario -->
                                                        @endif
                                                    </div>
                                                    <div class="mt-auto flex flex-wrap items-center justify-between gap-2 pt-3">
                                                        <form action="{{ route('user-list.destroy', $entry->id) }}" method="POST"
                                                            class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="text-red-400 hover:text-red-300 flex items-center gap-1 text-[10px] font-bold transition-colors">
                                                                <i class="fas fa-trash-alt"></i> Quitar
                                                            </button>
                                                        </form>
                                                        @if($media)
                                                            <a href="{{ route('media.show', $media->id) }}"
                                                                class="text-gray-400 hover:text-white flex items-center gap-1 text-[10px] font-bold transition-colors">
                                                                Detalles <i class="fas fa-arrow-right"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
@endsection